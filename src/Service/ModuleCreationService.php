<?php

namespace App\Service;

use App\Entity\Gwatch\ModuleTracking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ModuleCreationService
{
    private $entityManager;
    private $slugger;
    private $uploadDir;

    public function __construct(
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        string $uploadDir = '%kernel.project_root%/uploads'
    ) {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
        $this->uploadDir = $uploadDir;
    }

    public function createModule(
        string $moduleName,
        string $description,
        bool $isPublic,
        $owner,
        UploadedFile $chrFile,
        ?UploadedFile $chrsuppFile = null,
        ?UploadedFile $colFile = null,
        ?UploadedFile $indFile = null,
        ?UploadedFile $rPvalFile = null,
        ?UploadedFile $rRatioFile = null,
        ?UploadedFile $vIndFile = null,
        ?UploadedFile $rowFile = null,
        ?UploadedFile $valFile = null
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
        
        // Create chrsupp table if file is provided
        if ($chrsuppFile) {
            $this->createChrSuppTable($moduleId, $chrsuppFile);
        }
        
        // Create col table if file is provided
        if ($colFile) {
            $this->createColTable($moduleId, $colFile);
        }
        
        // Create ind table if file is provided
        if ($indFile) {
            $this->createIndTable($moduleId, $indFile);
        }
        
        // Create r_pval table if file is provided
        if ($rPvalFile) {
            $this->createRPvalTable($moduleId, $rPvalFile);
        }
        
        // Create r_ratio table if file is provided
        if ($rRatioFile) {
            $this->createRRatioTable($moduleId, $rRatioFile);
        }
        
        // Create v_ind table if file is provided
        if ($vIndFile) {
            $this->createVIndTable($moduleId, $vIndFile);
        }
        
        // Create row-based tables if file is provided
        if ($rowFile) {
            $this->createRowBasedTables($moduleId, $rowFile);
        }
        
        // Create value-based tables if file is provided
        if ($valFile) {
            $this->createValueBasedTables($moduleId, $valFile);
        }

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

        // Create the chr table based on the Chr entity structure
        $createTableSql = "CREATE TABLE IF NOT EXISTS `chr` (
            `chr` INT NOT NULL,
            `chrname` VARCHAR(255) NOT NULL,
            `len` INT NOT NULL,
            PRIMARY KEY (`chr`),
            INDEX `idx_chrname` (`chrname`)
        )";
        
        $this->entityManager->getConnection()->executeStatement($createTableSql);

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
}
