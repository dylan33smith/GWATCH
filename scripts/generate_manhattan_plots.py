#!/usr/bin/env python3
"""
Manhattan Plot Generator for SONGBIRD

This script generates manhattan plots for each test in a module and stores
the PNG images and metadata directly in the database.

Usage: python3 generate_manhattan_plots.py <host> <user> <password> <module_id>
"""

import mysql.connector
import matplotlib.pyplot as plt
import numpy as np
import json
import sys
import io
from typing import List, Dict, Tuple
import logging

# Set up logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

class ManhattanPlotGenerator:
    def __init__(self, host: str, user: str, password: str, module_id: int):
        """Initialize the generator with database connection details"""
        self.host = host
        self.user = user
        self.password = password
        self.module_id = module_id
        self.database_name = f"Module_{module_id}"
        self.connection = None
        self.cursor = None
        
    def connect_to_database(self):
        """Connect to the specific module database"""
        try:
            self.connection = mysql.connector.connect(
                host=self.host,
                user=self.user,
                password=self.password,
                database=self.database_name,
                charset='utf8mb4'
            )
            self.cursor = self.connection.cursor(dictionary=True)
            logger.info(f"Connected to {self.database_name} successfully")
        except mysql.connector.Error as e:
            raise Exception(f"Could not connect to module database {self.database_name}: {e}")
    
    def close_connection(self):
        """Close the database connection"""
        if self.cursor:
            self.cursor.close()
        if self.connection:
            self.connection.close()
            logger.info("Database connection closed")
    
    def get_test_data(self, test_number: int) -> List[Dict]:
        """Extract data for a specific test following the data flow"""
        query = """
        SELECT 
            p.v_ind,
            p.pval,
            v.ind,
            v.col as test_number,
            i.chr,
            i.nrow,
            c.test as test_name
        FROM pval p
        JOIN v_ind v ON p.v_ind = v.v_ind
        JOIN ind i ON v.ind = i.ind
        JOIN col c ON v.col = c.col
        WHERE v.col = %s
        ORDER BY i.chr, i.nrow
        """
        
        try:
            self.cursor.execute(query, (test_number,))
            data = self.cursor.fetchall()
            logger.info(f"Retrieved {len(data)} SNPs for test {test_number}")
            return data
        except mysql.connector.Error as e:
            raise Exception(f"Error retrieving test data: {e}")
    
    def calculate_coordinates(self, data: List[Dict], plot_width: int = 800, plot_height: int = 600) -> List[Dict]:
        """Calculate X,Y coordinates for plotting"""
        if not data:
            return []
        
        # Calculate genomic position ranges for each chromosome
        chr_ranges = {}
        current_pos = 0
        
        # Group data by chromosome and calculate ranges
        for row in data:
            chr_num = row['chr']
            if chr_num not in chr_ranges:
                chr_ranges[chr_num] = {'start': current_pos, 'end': current_pos}
                current_pos += 1000  # Arbitrary spacing between chromosomes
            chr_ranges[chr_num]['end'] = current_pos
        
        # Calculate coordinates based on actual genomic positions
        coordinates = []
        
        for row in data:
            # X coordinate: use actual genomic position (nrow) within chromosome
            chr_start = chr_ranges[row['chr']]['start']
            x_coord = chr_start + row['nrow']
            
            # Y coordinate: use actual -log10(p-value)
            pval_log = -np.log10(row['pval']) if row['pval'] > 0 else 0
            
            coordinates.append({
                'ind': row['ind'],
                'test_number': row['test_number'],
                'chr': row['chr'],
                'nrow': row['nrow'],
                'coordX': x_coord,  # Genomic position X coordinate
                'coordY': pval_log,  # -log10(p-value) Y coordinate
                'pval': row['pval'],
                'pval_log': pval_log
            })
        
        logger.info(f"Calculated coordinates for {len(coordinates)} SNPs")
        return coordinates
    
    def generate_plot(self, coordinates: List[Dict], test_number: int, plot_width: int = 800, plot_height: int = 600) -> bytes:
        """Generate manhattan plot and return PNG data as bytes"""
        if not coordinates:
            raise ValueError(f"No coordinates provided for test {test_number}")
        
        # Extract data for plotting
        chromosomes = [c['chr'] for c in coordinates]
        pvals = [c['pval'] for c in coordinates]
        
        # Create plot
        plt.figure(figsize=(12, 8))
        
        # Calculate X-axis positions based on actual SNP positions (nrow) within each chromosome
        x_positions = []
        chr_positions = {}
        current_x = 0
        
        # Group SNPs by chromosome and calculate X positions
        for coord in coordinates:
            chr_num = coord['chr']
            if chr_num not in chr_positions:
                chr_positions[chr_num] = {'start': current_x, 'snps': []}
            
            # Use the actual nrow value for X position within the chromosome
            x_pos = current_x + coord['nrow']
            x_positions.append(x_pos)
            chr_positions[chr_num]['snps'].append(x_pos)
            
            # Move to next chromosome after processing all SNPs in current chromosome
            if coord == coordinates[-1] or (len(coordinates) > 1 and coordinates[coordinates.index(coord) + 1]['chr'] != chr_num):
                current_x = max(chr_positions[chr_num]['snps']) + 1000  # Add spacing between chromosomes
        
        # Plot points colored by chromosome
        unique_chrs = sorted(list(set(chromosomes)))
        colors = ['#1f77b4', '#ff7f0e', '#2ca02c', '#d62728', '#9467bd', '#8c564b', '#e377c2', '#7f7f7f', '#bcbd22', '#17becf']
        
        for i, chr_num in enumerate(unique_chrs):
            chr_indices = [j for j, c in enumerate(chromosomes) if c == chr_num]
            chr_x = [x_positions[j] for j in chr_indices]
            chr_y = [-np.log10(pvals[j]) for j in chr_indices if pvals[j] > 0]
            chr_x_filtered = [x_positions[j] for j in chr_indices if pvals[j] > 0]
            
            if chr_x_filtered:  # Only plot if we have valid p-values
                plt.scatter(
                    chr_x_filtered,
                    chr_y,
                    c=colors[i % len(colors)], 
                    alpha=0.6,
                    s=20
                )
        
        # Add significance threshold line
        threshold = 3.0  # -log10(p-value) threshold
        plt.axhline(y=threshold, color='red', linestyle='--', alpha=0.7)
        
        # Set X-axis ticks to show chromosome boundaries
        chr_boundaries = []
        chr_labels = []
        for chr_num in unique_chrs:
            if chr_num in chr_positions:
                chr_boundaries.append(chr_positions[chr_num]['start'])
                chr_labels.append(f'{chr_num}')
        
        plt.xticks(chr_boundaries, chr_labels)
        plt.xlabel('Chromosome')
        plt.ylabel('-log10(p-value)')
        
        # Remove grid and legend for cleaner look
        plt.grid(False)
        
        # Save to bytes
        img_buffer = io.BytesIO()
        plt.savefig(img_buffer, format='png', dpi=300, bbox_inches='tight')
        img_buffer.seek(0)
        png_data = img_buffer.getvalue()
        plt.close()
        
        logger.info(f"Generated PNG plot for test {test_number} ({len(png_data)} bytes)")
        return png_data
    
    def extract_significant_snps(self, coordinates: List[Dict], threshold: float = 3.0) -> List[Dict]:
        """Extract SNPs above significance threshold"""
        significant = [
            {
                'ind': c['ind'],
                'test_number': c['test_number'],
                'chr': c['chr'],
                'nrow': c['nrow'],
                'coordX': c['coordX'],
                'coordY': c['coordY']
            }
            for c in coordinates if c['pval_log'] > threshold
        ]
        
        logger.info(f"Found {len(significant)} SNPs above threshold -log10(p-value) > {threshold}")
        return significant
    
    def store_png_directly(self, test_number: int, png_data: bytes) -> bool:
        """Store PNG data directly to mplot_png table"""
        try:
            query = "INSERT INTO mplot_png (test_number, png) VALUES (%s, %s) ON DUPLICATE KEY UPDATE png = VALUES(png)"
            self.cursor.execute(query, (test_number, png_data))
            self.connection.commit()
            logger.info(f"Stored PNG data for test {test_number}")
            return True
        except mysql.connector.Error as e:
            logger.error(f"Error storing PNG data: {e}")
            return False
    
    def store_metadata_directly(self, metadata: List[Dict]) -> bool:
        """Store metadata directly to mplot table"""
        if not metadata:
            logger.info("No metadata to store")
            return True
        
        try:
            # Clear existing metadata for this test
            test_number = metadata[0]['test_number']
            self.cursor.execute("DELETE FROM mplot WHERE test_number = %s", (test_number,))
            
            # Insert new metadata
            query = "INSERT INTO mplot (ind, test_number, chr, nrow, coordX, coordY) VALUES (%s, %s, %s, %s, %s, %s)"
            for row in metadata:
                self.cursor.execute(query, (
                    row['ind'], row['test_number'], row['chr'], 
                    row['nrow'], row['coordX'], row['coordY']
                ))
            
            self.connection.commit()
            logger.info(f"Stored metadata for {len(metadata)} SNPs in test {test_number}")
            return True
        except mysql.connector.Error as e:
            logger.error(f"Error storing metadata: {e}")
            return False
    
    def process_test(self, test_number: int) -> Tuple[bool, bool]:
        """Process a single test and return success status for PNG and metadata storage"""
        try:
            logger.info(f"Processing test {test_number}...")
            
            # Get data
            data = self.get_test_data(test_number)
            if not data:
                logger.warning(f"No data found for test {test_number}")
                return False, False
            
            # Calculate coordinates
            coordinates = self.calculate_coordinates(data)
            
            # Generate plot
            png_data = self.generate_plot(coordinates, test_number)
            
            # Extract significant SNPs
            significant_snps = self.extract_significant_snps(coordinates)
            
            # Store data directly in database
            png_success = self.store_png_directly(test_number, png_data)
            metadata_success = self.store_metadata_directly(significant_snps)
            
            logger.info(f"Test {test_number} completed - PNG: {png_success}, Metadata: {metadata_success}")
            return png_success, metadata_success
            
        except Exception as e:
            logger.error(f"Error processing test {test_number}: {e}")
            return False, False
    
    def get_all_test_numbers(self) -> List[int]:
        """Get all test numbers from the col table"""
        try:
            self.cursor.execute("SELECT DISTINCT col FROM col ORDER BY col")
            test_numbers = [row['col'] for row in self.cursor.fetchall()]
            logger.info(f"Found {len(test_numbers)} tests: {test_numbers}")
            return test_numbers
        except mysql.connector.Error as e:
            raise Exception(f"Error retrieving test numbers: {e}")
    
    def process_all_tests(self) -> Dict[str, int]:
        """Process all tests and return summary statistics"""
        test_numbers = self.get_all_test_numbers()
        
        if not test_numbers:
            logger.warning("No tests found to process")
            return {'total': 0, 'png_success': 0, 'metadata_success': 0, 'failed': 0}
        
        logger.info(f"Processing {len(test_numbers)} tests...")
        
        results = {'total': len(test_numbers), 'png_success': 0, 'metadata_success': 0, 'failed': 0}
        
        for test_number in test_numbers:
            try:
                png_success, metadata_success = self.process_test(test_number)
                
                if png_success:
                    results['png_success'] += 1
                if metadata_success:
                    results['metadata_success'] += 1
                if not png_success and not metadata_success:
                    results['failed'] += 1
                    
            except Exception as e:
                logger.error(f"Failed to process test {test_number}: {e}")
                results['failed'] += 1
        
        logger.info(f"Processing complete. Results: {results}")
        return results

def main():
    """Main function to run the manhattan plot generator"""
    if len(sys.argv) != 5:
        print("Usage: python3 generate_manhattan_plots.py <host> <user> <password> <module_id>")
        print("Example: python3 generate_manhattan_plots.py 127.0.0.1 gwatch_user 123457 39")
        sys.exit(1)
    
    host, user, password, module_id = sys.argv[1:]
    module_id = int(module_id)
    
    print(f"Starting Manhattan Plot Generation for Module_{module_id}")
    print(f"Host: {host}, User: {user}")
    print("-" * 50)
    
    generator = None
    try:
        # Create generator and connect to database
        generator = ManhattanPlotGenerator(host, user, password, module_id)
        generator.connect_to_database()
        
        # Process all tests
        results = generator.process_all_tests()
        
        # Print summary
        print("\n" + "=" * 50)
        print("PROCESSING COMPLETE")
        print("=" * 50)
        print(f"Total tests processed: {results['total']}")
        print(f"PNG generation successful: {results['png_success']}")
        print(f"Metadata storage successful: {results['metadata_success']}")
        print(f"Failed: {results['failed']}")
        
        if results['failed'] == 0:
            print("\n🎉 All tests processed successfully!")
        else:
            print(f"\n⚠️  {results['failed']} tests failed. Check logs for details.")
            
    except Exception as e:
        print(f"\n❌ Error: {e}")
        sys.exit(1)
    finally:
        if generator:
            generator.close_connection()

if __name__ == "__main__":
    main()
