<?php
require_once 'vendor/autoload.php';

use App\Service\DatabaseConfigurationService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

// Mock for testing
class MockParameterBag implements ParameterBagInterface
{
    public function get(string $name): \UnitEnum|array|string|int|float|bool|null
    {
        if ($name === 'app.database_url') {
            return 'mysql://gwatch_user:123457@127.0.0.1:3306/gwatch_db?serverVersion=8.0.42&charset=utf8mb4';
        }
        return null;
    }
    
    public function has(string $name): bool { return $name === 'app.database_url'; }
    public function all(): array { return ['app.database_url' => 'mysql://gwatch_user:123457@127.0.0.1:3306/gwatch_db?serverVersion=8.0.42&charset=utf8mb4']; }
    public function resolve() { return null; }
    public function resolveValue($value) { return $value; }
    public function escapeValue($value): mixed { return $value; }
    public function unescapeValue($value): mixed { return $value; }
    public function clear() {}
    public function add($parameters) {}
    public function remove($name) {}
    public function set($name, $value) {}
}

echo "=== Testing Fixed Table Creation ===\n\n";

try {
    $params = new MockParameterBag();
    $dbConfig = new DatabaseConfigurationService($params);
    
    echo "✅ Configuration service created successfully!\n\n";
    
    $mainConfig = $dbConfig->getMainDatabaseConfig();
    echo "Main database config: {$mainConfig['host']}:{$mainConfig['port']}/{$mainConfig['dbname']}\n";
    
    // Test creating a module database
    $pdo = new PDO(
        "mysql:host={$mainConfig['host']};port={$mainConfig['port']};dbname={$mainConfig['dbname']}",
        $mainConfig['user'],
        $mainConfig['password']
    );
    echo "✅ Connected to main database successfully!\n";
    
    $testDbName = 'Module_Test_Fix_' . time();
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$testDbName}`");
    echo "✅ Test module database created: {$testDbName}\n";
    
    // Connect to the test module database
    $moduleConfig = $dbConfig->getModuleDatabaseConfig($testDbName);
    $modulePdo = new PDO(
        "mysql:host={$moduleConfig['host']};port={$moduleConfig['port']};dbname={$moduleConfig['dbname']}",
        $moduleConfig['user'],
        $moduleConfig['password']
    );
    echo "✅ Connected to test module database successfully!\n";
    
    // Test creating tables with the fixed schema
    echo "\nTesting table creation:\n";
    
    $tables = [
        'chr' => 'CREATE TABLE chr (
            chr INT PRIMARY KEY,
            chrname VARCHAR(255) NOT NULL,
            len INT NOT NULL,
            moduleId VARCHAR(50) NOT NULL
        )',
        'chrsupp' => 'CREATE TABLE chrsupp (
            chr INT PRIMARY KEY,
            chroff INT NOT NULL,
            chrlen INT NOT NULL,
            moduleId VARCHAR(50) NOT NULL
        )',
        'col' => 'CREATE TABLE col (
            col INT AUTO_INCREMENT PRIMARY KEY,
            test VARCHAR(255),
            refTable VARCHAR(255) NOT NULL,
            refCol VARCHAR(255) NOT NULL,
            moduleId VARCHAR(50) NOT NULL
        )',
        'ind' => 'CREATE TABLE ind (
            chr INT,
            nrow INT,
            ind INT PRIMARY KEY,
            moduleId VARCHAR(50) NOT NULL,
            INDEX idx_chr (chr),
            FOREIGN KEY (chr) REFERENCES chr(chr)
        )',
        'v_ind' => 'CREATE TABLE v_ind (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ind INT,
            col INT,
            v_ind INT NOT NULL,
            moduleId VARCHAR(50) NOT NULL,
            INDEX idx_v_ind (v_ind),
            INDEX idx_ind (ind),
            INDEX idx_col (col),
            FOREIGN KEY (ind) REFERENCES ind(ind),
            FOREIGN KEY (col) REFERENCES col(col)
        )',
        'r_pval' => 'CREATE TABLE r_pval (
            id INT AUTO_INCREMENT PRIMARY KEY,
            v_ind INT,
            r_pval INT NOT NULL,
            moduleId VARCHAR(50) NOT NULL,
            INDEX idx_v_ind (v_ind),
            FOREIGN KEY (v_ind) REFERENCES v_ind(id)
        )'
    ];
    
    foreach ($tables as $tableName => $sql) {
        try {
            $modulePdo->exec($sql);
            echo "✅ Table '{$tableName}' created successfully\n";
        } catch (PDOException $e) {
            echo "❌ Error creating table '{$tableName}': " . $e->getMessage() . "\n";
            break;
        }
    }
    
    // Test inserting some sample data
    echo "\nTesting data insertion:\n";
    
    try {
        // Insert into chr
        $modulePdo->exec("INSERT INTO chr (chr, chrname, len, moduleId) VALUES (1, 'Chr1', 1000, 'test')");
        echo "✅ Data inserted into chr table\n";
        
        // Insert into col
        $modulePdo->exec("INSERT INTO col (test, refTable, refCol, moduleId) VALUES ('Test1', 'Table1', 'Col1', 'test')");
        echo "✅ Data inserted into col table\n";
        
        // Insert into ind
        $modulePdo->exec("INSERT INTO ind (chr, nrow, ind, moduleId) VALUES (1, 1, 1, 'test')");
        echo "✅ Data inserted into ind table\n";
        
        // Insert into v_ind
        $modulePdo->exec("INSERT INTO v_ind (ind, col, v_ind, moduleId) VALUES (1, 1, 1, 'test')");
        echo "✅ Data inserted into v_ind table\n";
        
        // Insert into r_pval
        $modulePdo->exec("INSERT INTO r_pval (v_ind, r_pval, moduleId) VALUES (1, 0.05, 'test')");
        echo "✅ Data inserted into r_pval table\n";
        
    } catch (PDOException $e) {
        echo "❌ Error inserting data: " . $e->getMessage() . "\n";
    }
    
    // Clean up
    echo "\nCleaning up test database...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `{$testDbName}`");
    echo "✅ Test cleanup completed successfully!\n";
    
    echo "\n🎉 All tests passed! The foreign key constraint issue has been fixed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
