<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvValidationService
{
    /**
     * Validates all CSV files against expected column structure
     */
    public function validateAllFiles(
        UploadedFile $chrFile,
        UploadedFile $chrsuppFile,
        UploadedFile $colFile,
        UploadedFile $indFile,
        UploadedFile $rPvalFile,
        UploadedFile $rRatioFile,
        UploadedFile $vIndFile,
        UploadedFile $rowFile,
        UploadedFile $valFile
    ): array {
        $errors = [];
        
        // Validate each file according to table_csv_mapping.txt
        $errors = array_merge($errors, $this->validateChrFile($chrFile));
        $errors = array_merge($errors, $this->validateChrsuppFile($chrsuppFile));
        $errors = array_merge($errors, $this->validateColFile($colFile));
        $errors = array_merge($errors, $this->validateIndFile($indFile));
        $errors = array_merge($errors, $this->validateRPvalFile($rPvalFile));
        $errors = array_merge($errors, $this->validateRRatioFile($rRatioFile));
        $errors = array_merge($errors, $this->validateVIndFile($vIndFile));
        $errors = array_merge($errors, $this->validateRowFile($rowFile));
        $errors = array_merge($errors, $this->validateValFile($valFile));
        
        return $errors;
    }
    
    /**
     * Validates chr.csv: should have 3 columns (chr, chrname, len)
     */
    private function validateChrFile(UploadedFile $file): array
    {
        $errors = [];
        $data = $this->readCsvFile($file);
        
        if (empty($data)) {
            $errors[] = "chr.csv is empty or could not be read";
            return $errors;
        }
        
        foreach ($data as $rowNum => $row) {
            if (count($row) < 3) {
                $errors[] = "chr.csv row " . ($rowNum + 1) . " has " . count($row) . " columns, expected 3 (chr, chrname, len)";
            }
            
            // Validate data types
            if (isset($row[0]) && !is_numeric($row[0])) {
                $errors[] = "chr.csv row " . ($rowNum + 1) . " column 1 (chr) should be numeric, got: " . $row[0];
            }
            if (isset($row[2]) && !is_numeric($row[2])) {
                $errors[] = "chr.csv row " . ($rowNum + 1) . " column 3 (len) should be numeric, got: " . $row[2];
            }
        }
        
        return $errors;
    }
    
    /**
     * Validates chrsupp.csv: should have 3 columns (chr, chroff, chrlen)
     */
    private function validateChrsuppFile(UploadedFile $file): array
    {
        $errors = [];
        $data = $this->readCsvFile($file);
        
        if (empty($data)) {
            $errors[] = "chrsupp.csv is empty or could not be read";
            return $errors;
        }
        
        foreach ($data as $rowNum => $row) {
            if (count($row) < 3) {
                $errors[] = "chrsupp.csv row " . ($rowNum + 1) . " has " . count($row) . " columns, expected 3 (chr, chroff, chrlen)";
            }
            
            // Validate data types
            if (isset($row[0]) && !is_numeric($row[0])) {
                $errors[] = "chrsupp.csv row " . ($rowNum + 1) . " column 1 (chr) should be numeric, got: " . $row[0];
            }
            if (isset($row[1]) && !is_numeric($row[1])) {
                $errors[] = "chrsupp.csv row " . ($rowNum + 1) . " column 2 (chroff) should be numeric, got: " . $row[1];
            }
            if (isset($row[2]) && !is_numeric($row[2])) {
                $errors[] = "chrsupp.csv row " . ($rowNum + 1) . " column 3 (chrlen) should be numeric, got: " . $row[2];
            }
        }
        
        return $errors;
    }
    
    /**
     * Validates col.csv: should have 4 columns (col, test, ref.table, ref.col)
     */
    private function validateColFile(UploadedFile $file): array
    {
        $errors = [];
        $data = $this->readCsvFile($file);
        
        if (empty($data)) {
            $errors[] = "col.csv is empty or could not be read";
            return $errors;
        }
        
        foreach ($data as $rowNum => $row) {
            if (count($row) < 4) {
                $errors[] = "col.csv row " . ($rowNum + 1) . " has " . count($row) . " columns, expected 4 (col, test, ref.table, ref.col)";
            }
            
            // Validate data types
            if (isset($row[0]) && !is_numeric($row[0])) {
                $errors[] = "col.csv row " . ($rowNum + 1) . " column 1 (col) should be numeric, got: " . $row[0];
            }
        }
        
        return $errors;
    }
    
    /**
     * Validates ind.csv: should have 3 columns (chr, nrow, ind)
     */
    private function validateIndFile(UploadedFile $file): array
    {
        $errors = [];
        $data = $this->readCsvFile($file);
        
        if (empty($data)) {
            $errors[] = "ind.csv is empty or could not be read";
            return $errors;
        }
        
        foreach ($data as $rowNum => $row) {
            if (count($row) < 3) {
                $errors[] = "ind.csv row " . ($rowNum + 1) . " has " . count($row) . " columns, expected 3 (chr, nrow, ind)";
            }
            
            // Validate data types
            if (isset($row[0]) && !is_numeric($row[0])) {
                $errors[] = "ind.csv row " . ($rowNum + 1) . " column 1 (chr) should be numeric, got: " . $row[0];
            }
            if (isset($row[1]) && !is_numeric($row[1])) {
                $errors[] = "ind.csv row " . ($rowNum + 1) . " column 2 (nrow) should be numeric, got: " . $row[1];
            }
            if (isset($row[2]) && !is_numeric($row[2])) {
                $errors[] = "ind.csv row " . ($rowNum + 1) . " column 3 (ind) should be numeric, got: " . $row[2];
            }
        }
        
        return $errors;
    }
    
    /**
     * Validates r_pval.csv: should have 2 columns (v_ind, r_pval)
     */
    private function validateRPvalFile(UploadedFile $file): array
    {
        $errors = [];
        $data = $this->readCsvFile($file);
        
        if (empty($data)) {
            $errors[] = "r_pval.csv is empty or could not be read";
            return $errors;
        }
        
        foreach ($data as $rowNum => $row) {
            if (count($row) < 2) {
                $errors[] = "r_pval.csv row " . ($rowNum + 1) . " has " . count($row) . " columns, expected 2 (v_ind, r_pval)";
            }
            
            // Validate data types
            if (isset($row[0]) && !is_numeric($row[0])) {
                $errors[] = "r_pval.csv row " . ($rowNum + 1) . " column 1 (v_ind) should be numeric, got: " . $row[0];
            }
            if (isset($row[1]) && !is_numeric($row[1])) {
                $errors[] = "r_pval.csv row " . ($rowNum + 1) . " column 2 (r_pval) should be numeric, got: " . $row[1];
            }
        }
        
        return $errors;
    }
    
    /**
     * Validates r_ratio.csv: should have 2 columns (v_ind, r_ratio)
     */
    private function validateRRatioFile(UploadedFile $file): array
    {
        $errors = [];
        $data = $this->readCsvFile($file);
        
        if (empty($data)) {
            $errors[] = "r_ratio.csv is empty or could not be read";
            return $errors;
        }
        
        foreach ($data as $rowNum => $row) {
            if (count($row) < 2) {
                $errors[] = "r_ratio.csv row " . ($rowNum + 1) . " has " . count($row) . " columns, expected 2 (v_ind, r_ratio)";
            }
            
            // Validate data types
            if (isset($row[0]) && !is_numeric($row[0])) {
                $errors[] = "r_ratio.csv row " . ($rowNum + 1) . " column 1 (v_ind) should be numeric, got: " . $row[0];
            }
            if (isset($row[1]) && !is_numeric($row[1])) {
                $errors[] = "r_ratio.csv row " . ($rowNum + 1) . " column 2 (r_ratio) should be numeric, got: " . $row[1];
            }
        }
        
        return $errors;
    }
    
    /**
     * Validates v_ind.csv: should have 3 columns (ind, col, v_ind)
     */
    private function validateVIndFile(UploadedFile $file): array
    {
        $errors = [];
        $data = $this->readCsvFile($file);
        
        if (empty($data)) {
            $errors[] = "v_ind.csv is empty or could not be read";
            return $errors;
        }
        
        foreach ($data as $rowNum => $row) {
            if (count($row) < 3) {
                $errors[] = "v_ind.csv row " . ($rowNum + 1) . " has " . count($row) . " columns, expected 3 (ind, col, v_ind)";
            }
            
            // Validate data types
            if (isset($row[0]) && !is_numeric($row[0])) {
                $errors[] = "v_ind.csv row " . ($rowNum + 1) . " column 1 (ind) should be numeric, got: " . $row[0];
            }
            if (isset($row[1]) && !is_numeric($row[1])) {
                $errors[] = "v_ind.csv row " . ($rowNum + 1) . " column 2 (col) should be numeric, got: " . $row[1];
            }
            if (isset($row[2]) && !is_numeric($row[2])) {
                $errors[] = "v_ind.csv row " . ($rowNum + 1) . " column 3 (v_ind) should be numeric, got: " . $row[2];
            }
        }
        
        return $errors;
    }
    
    /**
     * Validates row.csv: should have 5 columns (ind, alias, pos, allele, maf)
     */
    private function validateRowFile(UploadedFile $file): array
    {
        $errors = [];
        $data = $this->readCsvFile($file);
        
        if (empty($data)) {
            $errors[] = "row.csv is empty or could not be read";
            return $errors;
        }
        
        foreach ($data as $rowNum => $row) {
            if (count($row) < 5) {
                $errors[] = "row.csv row " . ($rowNum + 1) . " has " . count($row) . " columns, expected 5 (ind, alias, pos, allele, maf)";
            }
            
            // Validate data types
            if (isset($row[0]) && !is_numeric($row[0])) {
                $errors[] = "row.csv row " . ($rowNum + 1) . " column 1 (ind) should be numeric, got: " . $row[0];
            }
            if (isset($row[2]) && !is_numeric($row[2])) {
                $errors[] = "row.csv row " . ($rowNum + 1) . " column 3 (pos) should be numeric, got: " . $row[2];
            }
            if (isset($row[4]) && !is_numeric($row[4])) {
                $errors[] = "row.csv row " . ($rowNum + 1) . " column 5 (maf) should be numeric, got: " . $row[4];
            }
        }
        
        return $errors;
    }
    
    /**
     * Validates val.csv: should have 3 columns (v_ind, pval, ratio)
     */
    private function validateValFile(UploadedFile $file): array
    {
        $errors = [];
        $data = $this->readCsvFile($file);
        
        if (empty($data)) {
            $errors[] = "val.csv is empty or could not be read";
            return $errors;
        }
        
        foreach ($data as $rowNum => $row) {
            if (count($row) < 3) {
                $errors[] = "val.csv row " . ($rowNum + 1) . " has " . count($row) . " columns, expected 3 (v_ind, pval, ratio)";
            }
            
            // Validate data types
            if (isset($row[0]) && !is_numeric($row[0])) {
                $errors[] = "val.csv row " . ($rowNum + 1) . " column 1 (v_ind) should be numeric, got: " . $row[0];
            }
            if (isset($row[1]) && !is_numeric($row[1])) {
                $errors[] = "val.csv row " . ($rowNum + 1) . " column 2 (pval) should be numeric, got: " . $row[1];
            }
            if (isset($row[2]) && !is_numeric($row[2])) {
                $errors[] = "val.csv row " . ($rowNum + 1) . " column 3 (ratio) should be numeric, got: " . $row[2];
            }
        }
        
        return $errors;
    }
    
    /**
     * Helper method to read CSV file
     */
    private function readCsvFile(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            return [];
        }
        
        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            $data[] = $row;
        }
        
        fclose($handle);
        return $data;
    }
}
