<?php

namespace App\Service;

use App\Entity\SONGBIRD\ModuleTracking;
use App\Entity\SONGBIRD\User;
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
    private int $batchSize = 10000;

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

        // Create Manhattan plot tables
        $this->createManhattanPlotTables($moduleId);
        
        // Generate Manhattan plots for all tests in the module
        $this->generateManhattanPlots($moduleId);
        
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

        // Stream-insert CSV data
        $this->insertChrDataStream($chrFile->getPathname());
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

        // Stream-insert CSV data
        $this->insertChrSuppDataStream($chrsuppFile->getPathname());
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

        // Stream-insert CSV data
        $this->insertColDataStream($colFile->getPathname());
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

        // Stream-insert CSV data
        $this->insertIndDataStream($indFile->getPathname());
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

        // Stream-insert CSV data
        $this->insertRPvalDataStream($rPvalFile->getPathname());
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

        // Stream-insert CSV data
        $this->insertRRatioDataStream($rRatioFile->getPathname());
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

        // Stream-insert CSV data
        $this->insertVIndDataStream($vIndFile->getPathname());
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

        // Stream-insert CSV data into both tables
        $this->insertValDataStream($valFile->getPathname());
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

        // Stream-insert CSV data into all tables
        $this->insertRowDataStream($rowFile->getPathname());
    }

    private function insertRowDataStream(string $filePath): void
    {
        $connection = $this->entityManager->getConnection();
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $posStmt = $connection->prepare("INSERT INTO `pos` (`ind`, `pos`) VALUES (?, ?)");
        $aliasStmt = $connection->prepare("INSERT INTO `alias` (`ind`, `alias`) VALUES (?, ?)");
        $alleleStmt = $connection->prepare("INSERT INTO `allele` (`ind`, `allele`) VALUES (?, ?)");
        $mafStmt = $connection->prepare("INSERT INTO `maf` (`ind`, `maf`) VALUES (?, ?)");

        $count = 0;
        $connection->beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 5) {
                    continue;
                }
                $ind = (int)$row[0];
                $alias = $row[1];
                $pos = (int)$row[2];
                $allele = $row[3];
                $maf = (float)$row[4];

                $posStmt->executeStatement([$ind, $pos]);
                $aliasStmt->executeStatement([$ind, $alias]);
                $alleleStmt->executeStatement([$ind, $allele]);
                $mafStmt->executeStatement([$ind, $maf]);

                $count++;
                if ($count % $this->batchSize === 0) {
                    $connection->commit();
                    $connection->beginTransaction();
                }
            }
            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            fclose($handle);
            throw $e;
        }

        fclose($handle);
    }

    private function insertRPvalDataStream(string $filePath): void
    {
        $connection = $this->entityManager->getConnection();
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $stmt = $connection->prepare("INSERT INTO `r_pval` (`v_ind`, `r_pval`) VALUES (?, ?)");
        $count = 0;
        $connection->beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 2) {
                    continue;
                }
                $stmt->executeStatement([(int)$row[0], (int)$row[1]]);
                $count++;
                if ($count % $this->batchSize === 0) {
                    $connection->commit();
                    $connection->beginTransaction();
                }
            }
            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            fclose($handle);
            throw $e;
        }
        fclose($handle);
    }

    private function insertRRatioDataStream(string $filePath): void
    {
        $connection = $this->entityManager->getConnection();
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $stmt = $connection->prepare("INSERT INTO `r_ratio` (`v_ind`, `r_ratio`) VALUES (?, ?)");
        $count = 0;
        $connection->beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 2) {
                    continue;
                }
                $stmt->executeStatement([(int)$row[0], (int)$row[1]]);
                $count++;
                if ($count % $this->batchSize === 0) {
                    $connection->commit();
                    $connection->beginTransaction();
                }
            }
            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            fclose($handle);
            throw $e;
        }
        fclose($handle);
    }

    private function insertVIndDataStream(string $filePath): void
    {
        $connection = $this->entityManager->getConnection();
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $stmt = $connection->prepare("INSERT INTO `v_ind` (`ind`, `col`, `v_ind`) VALUES (?, ?, ?)");
        $count = 0;
        $connection->beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) {
                    continue;
                }
                $stmt->executeStatement([(int)$row[0], (int)$row[1], (int)$row[2]]);
                $count++;
                if ($count % $this->batchSize === 0) {
                    $connection->commit();
                    $connection->beginTransaction();
                }
            }
            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            fclose($handle);
            throw $e;
        }
        fclose($handle);
    }

    private function insertChrDataStream(string $filePath): void
    {
        $connection = $this->entityManager->getConnection();
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }
        $stmt = $connection->prepare("INSERT INTO `chr` (`chr`, `chrname`, `len`) VALUES (?, ?, ?)");
        $count = 0;
        $connection->beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) {
                    continue;
                }
                $chr = (int)$row[0];
                $chrname = $row[1];
                $len = (int)$row[2];
                $stmt->executeStatement([$chr, $chrname, $len]);
                $count++;
                if ($count % $this->batchSize === 0) {
                    $connection->commit();
                    $connection->beginTransaction();
                }
            }
            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            fclose($handle);
            throw $e;
        }
        fclose($handle);
    }

    private function insertChrSuppDataStream(string $filePath): void
    {
        $connection = $this->entityManager->getConnection();
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }
        $stmt = $connection->prepare("INSERT INTO `chrsupp` (`chr`, `chroff`, `chrlen`) VALUES (?, ?, ?)");
        $count = 0;
        $connection->beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) {
                    continue;
                }
                $stmt->executeStatement([(int)$row[0], (int)$row[1], (int)$row[2]]);
                $count++;
                if ($count % $this->batchSize === 0) {
                    $connection->commit();
                    $connection->beginTransaction();
                }
            }
            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            fclose($handle);
            throw $e;
        }
        fclose($handle);
    }

    private function insertColDataStream(string $filePath): void
    {
        $connection = $this->entityManager->getConnection();
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }
        $stmt = $connection->prepare("INSERT INTO `col` (`col`, `test`, `refTable`, `refCol`) VALUES (?, ?, ?, ?)");
        $count = 0;
        $connection->beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 4) {
                    continue;
                }
                $col = (int)$row[0];
                $test = $row[1] !== '' ? $row[1] : null;
                $refTable = $row[2];
                $refCol = $row[3];
                $stmt->executeStatement([$col, $test, $refTable, $refCol]);
                $count++;
                if ($count % $this->batchSize === 0) {
                    $connection->commit();
                    $connection->beginTransaction();
                }
            }
            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            fclose($handle);
            throw $e;
        }
        fclose($handle);
    }

    private function insertIndDataStream(string $filePath): void
    {
        $connection = $this->entityManager->getConnection();
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }
        $stmt = $connection->prepare("INSERT INTO `ind` (`chr`, `nrow`, `ind`) VALUES (?, ?, ?)");
        $count = 0;
        $connection->beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) {
                    continue;
                }
                $stmt->executeStatement([(int)$row[0], (int)$row[1], (int)$row[2]]);
                $count++;
                if ($count % $this->batchSize === 0) {
                    $connection->commit();
                    $connection->beginTransaction();
                }
            }
            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            fclose($handle);
            throw $e;
        }
        fclose($handle);
    }

    private function insertValDataStream(string $filePath): void
    {
        $connection = $this->entityManager->getConnection();
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $pvalStmt = $connection->prepare("INSERT INTO `pval` (`v_ind`, `pval`) VALUES (?, ?)");
        $ratioStmt = $connection->prepare("INSERT INTO `ratio` (`v_ind`, `ratio`) VALUES (?, ?)");
        $count = 0;
        $connection->beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) {
                    continue;
                }
                $vInd = (int)$row[0];
                $pval = (float)$row[1];
                $ratio = (float)$row[2];
                $pvalStmt->executeStatement([$vInd, $pval]);
                $ratioStmt->executeStatement([$vInd, $ratio]);
                $count++;
                if ($count % $this->batchSize === 0) {
                    $connection->commit();
                    $connection->beginTransaction();
                }
            }
            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            fclose($handle);
            throw $e;
        }
        fclose($handle);
    }

    private function createRadiusIndTable(string $moduleId, UploadedFile $radiusIndFile): void
    {
        // First, switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create the radius_ind table using schema service
        $this->schemaService->createTable($this->entityManager->getConnection(), 'radius_ind');

        // Stream-insert the CSV data
        $this->insertRadiusIndDataStream($radiusIndFile->getPathname());
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
                    $this->insertTopHitsStream($densityFile->getClientOriginalName(), $densityFile->getPathname());
                }
            }
        } catch (\Exception $e) {
            error_log("Error in createTopHitsTable: " . $e->getMessage() . " for module: " . $moduleId);
            throw $e;
        }
    }

    private function insertTopHitsStream(string $originalName, string $filePath): void
    {
        $connection = $this->entityManager->getConnection();
        if (!preg_match('/density_(\d+)\.csv$/', $originalName, $matches)) {
            throw new \Exception('Density file must be named like density_X.csv');
        }
        $radiusInd = (int)$matches[1];
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open density CSV file');
        }
        $stmt = $connection->prepare("INSERT INTO `top_hits` (`bits`, `radius_ind`, `v_ind`, `r_density`, `r_naive_p`, `left_ind`, `right_ind`, `left_cnt`, `right_cnt`, `density`, `naive_p`, `adj_p`, `cal_p`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL)");
        $count = 0;
        $first = true;
        $connection->beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if ($first) { $first = false; continue; }
                if (count($row) < 10) { continue; }
                $stmt->executeStatement([
                    (int)$row[0],
                    $radiusInd,
                    (int)$row[1],
                    (int)$row[2],
                    (int)$row[3],
                    (int)$row[4],
                    (int)$row[5],
                    (int)$row[6],
                    (int)$row[7],
                    $row[8] !== '' ? (float)$row[8] : null,
                    $row[9] !== '' ? (float)$row[9] : null,
                ]);
                $count++;
                if ($count % $this->batchSize === 0) {
                    $connection->commit();
                    $connection->beginTransaction();
                }
            }
            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            fclose($handle);
            throw $e;
        }
        fclose($handle);
    }

    private function insertRadiusIndDataStream(string $filePath): void
    {
        $connection = $this->entityManager->getConnection();
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open radius index CSV file');
        }
        $stmt = $connection->prepare("INSERT INTO `radius_ind` (`radius_ind`, `radius_type`, `radius_val`) VALUES (?, ?, ?)");
        $count = 0; $first = true;
        $connection->beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if ($first) { $first = false; continue; }
                if (count($row) < 3) { continue; }
                $stmt->executeStatement([(int)$row[0], $row[1], (int)$row[2]]);
                $count++;
                if ($count % $this->batchSize === 0) {
                    $connection->commit();
                    $connection->beginTransaction();
                }
            }
            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            fclose($handle);
            throw $e;
        }
        fclose($handle);
    }

    /**
     * Create Manhattan plot tables in the module database
     */
    private function createManhattanPlotTables(string $moduleId): void
    {
        try {
            error_log("Creating Manhattan plot tables for module: " . $moduleId);
            
            // Switch to the module database
            $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");
            
            // Create mplots table
            $createMplotsTable = "CREATE TABLE IF NOT EXISTS `mplots` (
                `test_id` int(11) NOT NULL COMMENT 'Test ID from col table',
                `test_name` varchar(255) NOT NULL COMMENT 'Test name from col.test',
                `plot_image` longblob NOT NULL COMMENT 'PNG image data',
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation timestamp',
                `plot_width` int(11) NOT NULL COMMENT 'Image width in pixels',
                `plot_height` int(11) NOT NULL COMMENT 'Image height in pixels',
                PRIMARY KEY (`test_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Manhattan plot images for each test'";
            
            $this->entityManager->getConnection()->executeStatement($createMplotsTable);
            error_log("Created mplots table for module: " . $moduleId);
            
            // Create significant_points table
            $createSignificantPointsTable = "CREATE TABLE IF NOT EXISTS `significant_points` (
                `test_id` int(11) NOT NULL COMMENT 'Foreign key to mplots.test_id',
                `snp_ind` int(11) NOT NULL COMMENT 'SNP index from ind.ind',
                `x_pixel` int(11) NOT NULL COMMENT 'X-coordinate on plot',
                `y_pixel` int(11) NOT NULL COMMENT 'Y-coordinate on plot',
                `p_value` double NOT NULL COMMENT 'Original p-value',
                `neg_log_p` double NOT NULL COMMENT '-log10(p-value)',
                `chromosome` int(11) NOT NULL COMMENT 'Chromosome number',
                PRIMARY KEY (`test_id`, `snp_ind`),
                KEY `idx_chromosome` (`chromosome`),
                KEY `idx_neg_log_p` (`neg_log_p`),
                CONSTRAINT `fk_significant_points_test` FOREIGN KEY (`test_id`) REFERENCES `mplots` (`test_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Significant points coordinates for Manhattan plots'";
            
            $this->entityManager->getConnection()->executeStatement($createSignificantPointsTable);
            error_log("Created significant_points table for module: " . $moduleId);
            
            error_log("Manhattan plot tables created successfully for module: " . $moduleId);
            
        } catch (\Exception $e) {
            error_log("Error creating Manhattan plot tables for module {$moduleId}: " . $e->getMessage());
            throw $e; // Re-throw this error as it's critical for module creation
        }
    }

    /**
     * Generate Manhattan plots for all tests in the module
     */
    private function generateManhattanPlots(string $moduleId): void
    {
        try {
            error_log("Starting Manhattan plot generation for module: " . $moduleId);
            
            // Get the path to the Python script
            $scriptPath = $this->getProjectRoot() . '/scripts/generate_manhattan_plots.py';
            
            if (!file_exists($scriptPath)) {
                error_log("Manhattan plot script not found at: " . $scriptPath);
                return;
            }
            
            // Build the command to execute the Python script
            $command = sprintf(
                'python3 %s --database %s 2>&1',
                escapeshellarg($scriptPath),
                escapeshellarg($moduleId)
            );
            
            error_log("Executing command: " . $command);
            
            // Execute the Python script
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                error_log("Manhattan plot generation failed with return code: " . $returnCode);
                error_log("Output: " . implode("\n", $output));
            } else {
                error_log("Manhattan plot generation completed successfully for module: " . $moduleId);
                if (!empty($output)) {
                    error_log("Output: " . implode("\n", $output));
                }
            }
            
        } catch (\Exception $e) {
            error_log("Error generating Manhattan plots for module {$moduleId}: " . $e->getMessage());
            // Don't throw the exception - Manhattan plot generation failure shouldn't break module creation
        }
    }

    /**
     * Get the project root directory
     */
    private function getProjectRoot(): string
    {
        return dirname(__DIR__, 2); // Go up two levels from src/Service to project root
    }
}
