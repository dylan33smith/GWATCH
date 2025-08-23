<?php

namespace App\Service;

use App\Entity\Gwatch\ModuleTracking;
use App\Entity\Gwatch\User;
use App\Service\ModuleSchemaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ModuleCreationService
{
    private $entityManager;
    private $schemaService;
    private $slugger;
    private $uploadDir;

    public function __construct(
        EntityManagerInterface $entityManager,
        ModuleSchemaService $schemaService,
        SluggerInterface $slugger,
        string $uploadDir = '%kernel.project_root%/uploads'
    ) {
        $this->entityManager = $entityManager;
        $this->schemaService = $schemaService;
        $this->slugger = $slugger;
        $this->uploadDir = $uploadDir;
    }

    /**
     * Create a new module with all associated data
     * 
     * @param string $moduleName Name of the module
     * @param string $description Description of the module
     * @param bool $isPublic Whether the module should be public
     * @param User $owner User who owns the module
     * @param UploadedFile $chrFile Chromosome file
     * @param UploadedFile $chrsuppFile Chromosome supplement file
     * @param UploadedFile $colFile Column file
     * @param UploadedFile $indFile Index file
     * @param UploadedFile $rPvalFile R p-value file
     * @param UploadedFile $rRatioFile R ratio file
     * @param UploadedFile $vIndFile Variant index file
     * @param UploadedFile $rowFile Row data file
     * @param UploadedFile $valFile Value data file
     * @param array $densityFiles Array of density files (e.g., [density_1.csv, density_3.csv])
     * @param UploadedFile|null $radiusIndFile Radius index file (optional)
     * @return ModuleTracking The created module tracking entity
     * @throws \Exception If module creation fails
     */
    public function createModule(
        string $moduleName,
        string $description,
        bool $isPublic,
        User $owner,
        UploadedFile $chrFile,
        UploadedFile $chrsuppFile,
        UploadedFile $colFile,
        UploadedFile $indFile,
        UploadedFile $rPvalFile,
        UploadedFile $rRatioFile,
        UploadedFile $vIndFile,
        UploadedFile $rowFile,
        UploadedFile $valFile,
        array $densityFiles = [],
        ?UploadedFile $radiusIndFile = null
    ): ModuleTracking {
        // Create the module tracking entry first
        $moduleTracking = new ModuleTracking();
        $moduleTracking->setName($moduleName);
        $moduleTracking->setDescription($description);
        $moduleTracking->setPublic($isPublic);
        $moduleTracking->setOwner($owner);

        // Persist to get the ID
        $this->entityManager->persist($moduleTracking);
        $this->entityManager->flush();

        // Now create the module database and table using the ID
        $moduleId = 'Module_' . $moduleTracking->getId();
        $this->createModuleDatabase($moduleId);
        $this->createChrTable($moduleId, $chrFile);
        $this->createChrSuppTable($moduleId, $chrsuppFile);
        $this->createColTable($moduleId, $colFile);
        $this->createIndTable($moduleId, $indFile);
        $this->createRPvalTable($moduleId, $rPvalFile);
        $this->createRRatioTable($moduleId, $rRatioFile);
        $this->createVIndTable($moduleId, $vIndFile);
        $this->createRowBasedTables($moduleId, $rowFile);
        $this->createValueBasedTables($moduleId, $valFile);

        // Create radius_ind table if radius index file is provided
        if ($radiusIndFile !== null) {
            $this->createRadiusIndTable($moduleId, $radiusIndFile);
        }

        // Create top_hits table if density files are provided
        if (!empty($densityFiles)) {
            $this->createTopHitsTable($moduleId, $densityFiles);
        }

        // Create mplot tables for manhattan plots
        $this->createMplotTables($moduleId);
        
        return $moduleTracking;
    }

    private function createModuleDatabase(string $moduleId): void
    {
        $sql = "CREATE DATABASE IF NOT EXISTS `{$moduleId}`";
        $this->entityManager->getConnection()->executeStatement($sql);
    }

    private function createChrTable(string $moduleId, UploadedFile $chrFile): void
    {
        // First, switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create the chr table using schema service
        $this->schemaService->createTable($this->entityManager->getConnection(), 'chr');

        // Parse and insert the CSV data
        $csvData = $this->parseCsvFileWithoutHeaders($chrFile);
        if (!empty($csvData)) {
            $this->insertChrData($moduleId, $csvData);
        }
    }

    private function createChrSuppTable(string $moduleId, UploadedFile $chrsuppFile): void
    {
        // First, switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create the chrsupp table based on the ChrSupp entity structure
        $createTableSql = "CREATE TABLE IF NOT EXISTS `chrsupp` (
            `chr` INT NOT NULL,
            `chroff` INT NOT NULL,
            `chrlen` INT NOT NULL,
            PRIMARY KEY (`chr`),
            INDEX `idx_chr` (`chr`)
        )";
        
        $this->entityManager->getConnection()->executeStatement($createTableSql);

        // Parse and insert the CSV data
        $csvData = $this->parseChrSuppCsvFile($chrsuppFile);
        if (!empty($csvData)) {
            $this->insertChrSuppData($moduleId, $csvData);
        }
    }

    private function createColTable(string $moduleId, UploadedFile $colFile): void
    {
        // First, switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create the col table based on the Col entity structure
        $createTableSql = "CREATE TABLE IF NOT EXISTS `col` (
            `col` INT NOT NULL,
            `test` VARCHAR(255) NULL,
            `refTable` VARCHAR(255) NOT NULL,
            `refCol` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`col`)
        )";
        
        $this->entityManager->getConnection()->executeStatement($createTableSql);

        // Parse and insert the CSV data
        $csvData = $this->parseColCsvFile($colFile);
        if (!empty($csvData)) {
            $this->insertColData($moduleId, $csvData);
        }
    }

    private function createIndTable(string $moduleId, UploadedFile $indFile): void
    {
        // First, switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create the ind table based on the Ind entity structure
        $createTableSql = "CREATE TABLE IF NOT EXISTS `ind` (
            `chr` INT NOT NULL,
            `nrow` INT NOT NULL,
            `ind` INT NOT NULL,
            PRIMARY KEY (`chr`, `nrow`),
            INDEX `idx_ind` (`ind`)
        )";
        
        $this->entityManager->getConnection()->executeStatement($createTableSql);

        // Parse and insert the CSV data
        $csvData = $this->parseIndCsvFile($indFile);
        if (!empty($csvData)) {
            $this->insertIndData($moduleId, $csvData);
        }
    }

    private function createRPvalTable(string $moduleId, UploadedFile $rPvalFile): void
    {
        // First, switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create the r_pval table based on the RPval entity structure
        $createTableSql = "CREATE TABLE IF NOT EXISTS `r_pval` (
            `v_ind` INT NOT NULL,
            `r_pval` INT NOT NULL,
            PRIMARY KEY (`v_ind`)
        )";
        
        $this->entityManager->getConnection()->executeStatement($createTableSql);

        // Parse and insert the CSV data
        $csvData = $this->parseRPvalCsvFile($rPvalFile);
        if (!empty($csvData)) {
            $this->insertRPvalData($moduleId, $csvData);
        }
    }

    private function createRRatioTable(string $moduleId, UploadedFile $rRatioFile): void
    {
        // First, switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create the r_ratio table based on the RRatio entity structure
        $createTableSql = "CREATE TABLE IF NOT EXISTS `r_ratio` (
            `v_ind` INT NOT NULL,
            `r_ratio` INT NOT NULL,
            PRIMARY KEY (`v_ind`)
        )";
        
        $this->entityManager->getConnection()->executeStatement($createTableSql);

        // Parse and insert the CSV data
        $csvData = $this->parseRRatioCsvFile($rRatioFile);
        if (!empty($csvData)) {
            $this->insertRRatioData($moduleId, $csvData);
        }
    }

    private function createVIndTable(string $moduleId, UploadedFile $vIndFile): void
    {
        // First, switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create the v_ind table based on the VInd entity structure
        $createTableSql = "CREATE TABLE IF NOT EXISTS `v_ind` (
            `ind` INT NOT NULL,
            `col` INT NOT NULL,
            `v_ind` INT NOT NULL,
            PRIMARY KEY (`ind`, `col`),
            INDEX `idx_v_ind` (`v_ind`)
        )";
        
        $this->entityManager->getConnection()->executeStatement($createTableSql);

        // Parse and insert the CSV data
        $csvData = $this->parseVIndCsvFile($vIndFile);
        if (!empty($csvData)) {
            $this->insertVIndData($moduleId, $csvData);
        }
    }

    private function createValueBasedTables(string $moduleId, UploadedFile $valFile): void
    {
        // First, switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create the pval table based on the Pval entity structure
        $createPvalTableSql = "CREATE TABLE IF NOT EXISTS `pval` (
            `v_ind` INT NOT NULL,
            `pval` FLOAT NOT NULL,
            PRIMARY KEY (`v_ind`)
        )";
        $this->entityManager->getConnection()->executeStatement($createPvalTableSql);

        // Create the ratio table based on the Ratio entity structure
        $createRatioTableSql = "CREATE TABLE IF NOT EXISTS `ratio` (
            `v_ind` INT NOT NULL,
            `ratio` FLOAT NOT NULL,
            PRIMARY KEY (`v_ind`)
        )";
        $this->entityManager->getConnection()->executeStatement($createRatioTableSql);

        // Parse and insert the CSV data into both tables
        $csvData = $this->parseValCsvFile($valFile);
        if (!empty($csvData)) {
            $this->insertValData($moduleId, $csvData);
        }
    }

    private function createRowBasedTables(string $moduleId, UploadedFile $rowFile): void
    {
        // First, switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create the pos table based on the Pos entity structure
        $createPosTableSql = "CREATE TABLE IF NOT EXISTS `pos` (
            `ind` INT NOT NULL,
            `pos` INT NOT NULL,
            PRIMARY KEY (`ind`),
            INDEX `idx_pos` (`pos`)
        )";
        $this->entityManager->getConnection()->executeStatement($createPosTableSql);

        // Create the alias table based on the Alias entity structure
        $createAliasTableSql = "CREATE TABLE IF NOT EXISTS `alias` (
            `ind` INT NOT NULL,
            `alias` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`ind`),
            INDEX `idx_alias` (`alias`)
        )";
        $this->entityManager->getConnection()->executeStatement($createAliasTableSql);

        // Create the allele table based on the Allele entity structure
        $createAlleleTableSql = "CREATE TABLE IF NOT EXISTS `allele` (
            `ind` INT NOT NULL,
            `allele` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`ind`),
            INDEX `idx_allele` (`allele`)
        )";
        $this->entityManager->getConnection()->executeStatement($createAlleleTableSql);

        // Create the maf table based on the Maf entity structure
        $createMafTableSql = "CREATE TABLE IF NOT EXISTS `maf` (
            `ind` INT NOT NULL,
            `maf` FLOAT NOT NULL,
            PRIMARY KEY (`ind`),
            INDEX `idx_maf` (`maf`)
        )";
        $this->entityManager->getConnection()->executeStatement($createMafTableSql);

        // Parse and insert the CSV data into all tables
        $csvData = $this->parseRowCsvFile($rowFile);
        if (!empty($csvData)) {
            $this->insertRowData($moduleId, $csvData);
        }
    }

    private function parseRowCsvFile(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 5) {
                $data[] = [
                    'ind' => (int)$row[0],      // CSV column 1
                    'alias' => $row[1],          // CSV column 2
                    'pos' => (int)$row[2],       // CSV column 3
                    'allele' => $row[3],         // CSV column 4
                    'maf' => (float)$row[4]      // CSV column 5
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    private function insertRowData(string $moduleId, array $data): void
    {
        if (empty($data)) {
            return;
        }

        // Insert into pos table
        $posSql = "INSERT INTO `pos` (`ind`, `pos`) VALUES (?, ?)";
        $posStmt = $this->entityManager->getConnection()->prepare($posSql);

        // Insert into alias table
        $aliasSql = "INSERT INTO `alias` (`ind`, `alias`) VALUES (?, ?)";
        $aliasStmt = $this->entityManager->getConnection()->prepare($aliasSql);

        // Insert into allele table
        $alleleSql = "INSERT INTO `allele` (`ind`, `allele`) VALUES (?, ?)";
        $alleleStmt = $this->entityManager->getConnection()->prepare($alleleSql);

        // Insert into maf table
        $mafSql = "INSERT INTO `maf` (`ind`, `maf`) VALUES (?, ?)";
        $mafStmt = $this->entityManager->getConnection()->prepare($mafSql);
        
        foreach ($data as $row) {
            $posStmt->executeStatement([$row['ind'], $row['pos']]);
            $aliasStmt->executeStatement([$row['ind'], $row['alias']]);
            $alleleStmt->executeStatement([$row['ind'], $row['allele']]);
            $mafStmt->executeStatement([$row['ind'], $row['maf']]);
        }
    }

    private function parseRPvalCsvFile(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 2) {
                $data[] = [
                    'v_ind' => (int)$row[0],
                    'r_pval' => (int)$row[1]
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    private function parseRRatioCsvFile(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 2) {
                $data[] = [
                    'v_ind' => (int)$row[0],
                    'r_ratio' => (int)$row[1]
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    private function parseVIndCsvFile(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) {
                $data[] = [
                    'ind' => (int)$row[0],
                    'col' => (int)$row[1],
                    'v_ind' => (int)$row[2]
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    private function insertRPvalData(string $moduleId, array $data): void
    {
        if (empty($data)) {
            return;
        }

        $sql = "INSERT INTO `r_pval` (`v_ind`, `r_pval`) VALUES (?, ?)";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        
        foreach ($data as $row) {
            $stmt->executeStatement([$row['v_ind'], $row['r_pval']]);
        }
    }

    private function insertRRatioData(string $moduleId, array $data): void
    {
        if (empty($data)) {
            return;
        }

        $sql = "INSERT INTO `r_ratio` (`v_ind`, `r_ratio`) VALUES (?, ?)";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        
        foreach ($data as $row) {
            $stmt->executeStatement([$row['v_ind'], $row['r_ratio']]);
        }
    }

    private function insertVIndData(string $moduleId, array $data): void
    {
        if (empty($data)) {
            return;
        }

        $sql = "INSERT INTO `v_ind` (`ind`, `col`, `v_ind`) VALUES (?, ?, ?)";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        
        foreach ($data as $row) {
            $stmt->executeStatement([$row['ind'], $row['col'], $row['v_ind']]);
        }
    }

    private function parseCsvFileWithoutHeaders(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 2) {
                $data[] = [
                    'chr' => (int)$row[0],
                    'chrname' => 'Chr' . $row[0], // Generate chrname from chr number
                    'len' => (int)$row[1]
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    private function insertChrData(string $moduleId, array $data): void
    {
        if (empty($data)) {
            return;
        }

        $sql = "INSERT INTO `chr` (`chr`, `chrname`, `len`) VALUES (?, ?, ?)";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        
        foreach ($data as $row) {
            $stmt->executeStatement([$row['chr'], $row['chrname'], $row['len']]);
        }
    }

    private function parseChrSuppCsvFile(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) {
                $data[] = [
                    'chr' => (int)$row[0],
                    'chroff' => (int)$row[1],
                    'chrlen' => (int)$row[2]
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    private function insertChrSuppData(string $moduleId, array $data): void
    {
        if (empty($data)) {
            return;
        }

        $sql = "INSERT INTO `chrsupp` (`chr`, `chroff`, `chrlen`) VALUES (?, ?, ?)";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        
        foreach ($data as $row) {
            $stmt->executeStatement([$row['chr'], $row['chroff'], $row['chrlen']]);
        }
    }

    private function parseColCsvFile(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 4) {
                $data[] = [
                    'col' => (int)$row[0],
                    'test' => $row[1] ?: null,  // test can be null
                    'refTable' => $row[2],
                    'refCol' => $row[3]
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    private function insertColData(string $moduleId, array $data): void
    {
        if (empty($data)) {
            return;
        }

        $sql = "INSERT INTO `col` (`col`, `test`, `refTable`, `refCol`) VALUES (?, ?, ?, ?)";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        
        foreach ($data as $row) {
            $stmt->executeStatement([$row['col'], $row['test'], $row['refTable'], $row['refCol']]);
        }
    }

    private function parseIndCsvFile(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) {
                $data[] = [
                    'chr' => (int)$row[0],
                    'nrow' => (int)$row[1],
                    'ind' => (int)$row[2]
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    private function insertIndData(string $moduleId, array $data): void
    {
        if (empty($data)) {
            return;
        }

        $sql = "INSERT INTO `ind` (`chr`, `nrow`, `ind`) VALUES (?, ?, ?)";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        
        foreach ($data as $row) {
            $stmt->executeStatement([$row['chr'], $row['nrow'], $row['ind']]);
        }
    }

    private function parseValCsvFile(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 2) {
                $data[] = [
                    'v_ind' => (int)$row[0],
                    'pval' => (float)$row[1],
                    'ratio' => (float)$row[2]
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    private function insertValData(string $moduleId, array $data): void
    {
        if (empty($data)) {
            return;
        }

        $pvalSql = "INSERT INTO `pval` (`v_ind`, `pval`) VALUES (?, ?)";
        $pvalStmt = $this->entityManager->getConnection()->prepare($pvalSql);

        $ratioSql = "INSERT INTO `ratio` (`v_ind`, `ratio`) VALUES (?, ?)";
        $ratioStmt = $this->entityManager->getConnection()->prepare($ratioSql);
        
        foreach ($data as $row) {
            $pvalStmt->executeStatement([$row['v_ind'], $row['pval']]);
            $ratioStmt->executeStatement([$row['v_ind'], $row['ratio']]);
        }
    }

    private function createRadiusIndTable(string $moduleId, UploadedFile $radiusIndFile): void
    {
        // First, switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create the radius_ind table using schema service
        $this->schemaService->createTable($this->entityManager->getConnection(), 'radius_ind');

        // Parse and insert the CSV data
        $csvData = $this->parseRadiusIndCsvFile($radiusIndFile);
        if (!empty($csvData)) {
            $this->insertRadiusIndData($moduleId, $csvData);
        }
    }

    private function createTopHitsTable(string $moduleId, array $densityFiles): void
    {
        try {
            // First, switch to the module database
            $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

            // Create the top_hits table using schema service
            $this->schemaService->createTable($this->entityManager->getConnection(), 'top_hits');

            // Process each density file
            foreach ($densityFiles as $densityFile) {
                if ($densityFile !== null) {
                    $csvData = $this->parseDensityCsvFile($densityFile);
                    if (!empty($csvData)) {
                        $this->insertTopHitsData($moduleId, $csvData);
                    }
                }
            }
        } catch (\Exception $e) {
            error_log("Error in createTopHitsTable: " . $e->getMessage() . " for module: " . $moduleId);
            throw $e;
        }
    }

    private function parseDensityCsvFile(UploadedFile $file): array
    {
        try {
            $handle = fopen($file->getPathname(), 'r');
            if (!$handle) {
                throw new \Exception('Could not open density CSV file');
            }

            // Extract radius_ind from filename (e.g., density_7.csv -> 7)
            $filename = $file->getClientOriginalName();
            error_log("Parsing density file: " . $filename);
            
            if (!preg_match('/density_(\d+)\.csv$/', $filename, $matches)) {
                throw new \Exception('Density file must be named in format: density_X.csv where X is the radius index');
            }
            $radiusInd = (int)$matches[1];
            error_log("Extracted radius_ind: " . $radiusInd);

            $data = [];
            $firstRow = true;
            $rowCount = 0;
            while (($row = fgetcsv($handle)) !== false) {
                if ($firstRow) {
                    $firstRow = false; // Skip header row
                    error_log("Skipping header row: " . implode(',', $row));
                    continue;
                }
                
                if (count($row) >= 10) {
                    $data[] = [
                        'bits' => (int)$row[0],           // CSV column 1: bits
                        'v_ind' => (int)$row[1],          // CSV column 2: v_ind
                        'r_density' => (int)$row[2],      // CSV column 3: r_density
                        'r_naive_p' => (int)$row[3],      // CSV column 4: r_naive_p
                        'left_ind' => (int)$row[4],       // CSV column 5: left_ind
                        'right_ind' => (int)$row[5],      // CSV column 6: right_ind
                        'left_cnt' => (int)$row[6],       // CSV column 7: left_cnt
                        'right_cnt' => (int)$row[7],      // CSV column 8: right_cnt
                        'density' => $row[8] ? (float)$row[8] : null,    // CSV column 9: density
                        'naive_p' => $row[9] ? (float)$row[9] : null,   // CSV column 10: naive_p
                        'radius_ind' => $radiusInd,       // From filename (e.g., density_7.csv -> 7)
                        'adj_p' => null,                  // Not in CSV, set to null
                        'cal_p' => null                   // Not in CSV, set to null
                    ];
                    $rowCount++;
                } else {
                    error_log("Skipping row with insufficient columns: " . count($row) . " columns found, need 10");
                }
            }

            fclose($handle);
            error_log("Parsed " . $rowCount . " data rows from density file");
            return $data;
        } catch (\Exception $e) {
            error_log("Error in parseDensityCsvFile: " . $e->getMessage());
            throw $e;
        }
    }

    private function insertTopHitsData(string $moduleId, array $data): void
    {
        try {
            if (empty($data)) {
                error_log("No data to insert for top_hits table in module: " . $moduleId);
                return;
            }

            error_log("Inserting " . count($data) . " rows into top_hits table for module: " . $moduleId);

            $sql = "INSERT INTO `top_hits` (`bits`, `radius_ind`, `v_ind`, `r_density`, `r_naive_p`, `left_ind`, `right_ind`, `left_cnt`, `right_cnt`, `density`, `naive_p`, `adj_p`, `cal_p`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->entityManager->getConnection()->prepare($sql);
            
            $insertedCount = 0;
            foreach ($data as $row) {
                $stmt->executeStatement([
                    $row['bits'],
                    $row['radius_ind'],
                    $row['v_ind'],
                    $row['r_density'],
                    $row['r_naive_p'],
                    $row['left_ind'],
                    $row['right_ind'],
                    $row['left_cnt'],
                    $row['right_cnt'],
                    $row['density'],
                    $row['naive_p'],
                    $row['adj_p'],
                    $row['cal_p']
                ]);
                $insertedCount++;
            }
            
            error_log("Successfully inserted " . $insertedCount . " rows into top_hits table for module: " . $moduleId);
        } catch (\Exception $e) {
            error_log("Error in insertTopHitsData: " . $e->getMessage() . " for module: " . $moduleId);
            throw $e;
        }
    }

    private function parseRadiusIndCsvFile(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \Exception('Could not open radius index CSV file');
        }

        $data = [];
        $firstRow = true;
        while (($row = fgetcsv($handle)) !== false) {
            if ($firstRow) {
                $firstRow = false; // Skip header row
                continue;
            }
            
            if (count($row) >= 3) {
                $data[] = [
                    'radius_ind' => (int)$row[0],     // CSV column 1: radius_ind
                    'radius_type' => $row[1],         // CSV column 2: radius_type
                    'radius_val' => (int)$row[2]      // CSV column 3: radius_val
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    private function insertRadiusIndData(string $moduleId, array $data): void
    {
        if (empty($data)) {
            return;
        }

        // Ensure we're using the correct module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        $sql = "INSERT INTO `radius_ind` (`radius_ind`, `radius_type`, `radius_val`) VALUES (?, ?, ?)";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        
        foreach ($data as $row) {
            $stmt->executeStatement([
                $row['radius_ind'],
                $row['radius_type'],
                $row['radius_val']
            ]);
        }
    }

    private function createMplotTables(string $moduleId): void
    {
        // First, switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create the mplot_png table using schema service
        $this->schemaService->createTable($this->entityManager->getConnection(), 'mplot_png');

        // Create the mplot table using schema service
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");
        $this->schemaService->createTable($this->entityManager->getConnection(), 'mplot');
        
        // Generate manhattan plots using Python script
        $this->generateManhattanPlots($moduleId);
    }
    
    private function generateManhattanPlots(string $moduleId): void
    {
        try {
            // Extract module ID number from the database name
            $moduleIdNumber = str_replace('Module_', '', $moduleId);
            
            // Get database connection parameters from environment
            $dbHost = $_ENV['DATABASE_HOST'] ?? '127.0.0.1';
            $dbUser = $_ENV['DATABASE_USER'] ?? 'gwatch_user';
            $dbPassword = $_ENV['DATABASE_PASSWORD'] ?? '123457';
            
            // Build command to run Python script
            $scriptPath = __DIR__ . '/../../scripts/generate_manhattan_plots.py';
            $command = sprintf(
                'python3 %s %s %s %s %s 2>&1',
                escapeshellarg($scriptPath),
                escapeshellarg($dbHost),
                escapeshellarg($dbUser),
                escapeshellarg($dbPassword),
                escapeshellarg($moduleIdNumber)
            );
            
            // Execute the Python script
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                error_log("Failed to generate manhattan plots for {$moduleId}: " . implode("\n", $output));
                // Don't throw exception - plot generation failure shouldn't prevent module creation
            } else {
                error_log("Successfully generated manhattan plots for {$moduleId}");
            }
            
        } catch (\Exception $e) {
            error_log("Error generating manhattan plots for {$moduleId}: " . $e->getMessage());
            // Don't throw exception - plot generation failure shouldn't prevent module creation
        }
    }
}
