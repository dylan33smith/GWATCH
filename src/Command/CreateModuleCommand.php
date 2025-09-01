<?php

namespace App\Command;

use App\Entity\Gwatch\User;
use App\Entity\Gwatch\ModuleTracking;
use App\Repository\UserRepository;
use App\Service\ModuleCreationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(
    name: 'app:create-module',
    description: 'Create a new module from a ZIP file containing CSV data',
)]
class CreateModuleCommand extends Command
{
    public function __construct(
        private ModuleCreationService $moduleCreationService,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('zip-file', InputArgument::REQUIRED, 'Path to ZIP file containing CSV data')
            ->addArgument('module-name', InputArgument::REQUIRED, 'Name of the module')
            ->addArgument('description', InputArgument::REQUIRED, 'Description of the module')
            ->addOption('owner', 'o', InputOption::VALUE_REQUIRED, 'Username of the module owner', 'admin')
            ->addOption('public', 'p', InputOption::VALUE_NONE, 'Make the module public')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force creation even if validation fails')
            ->setHelp(<<<'HELP'
This command creates a new module from a ZIP file containing CSV data.

Required CSV files:
- chr.csv - Chromosome data
- chrsupp.csv - Chromosome supplement data
- col.csv - Column reference data
- ind.csv - Index data
- r_pval.csv - R p-value data
- r_ratio.csv - R ratio data
- v_ind.csv - Variant index data
- row.csv - Row-based data (pos, alias, allele, maf)
- val.csv - Value-based data (pval, ratio)
- density_X.csv files (e.g., density_1.csv, density_2.csv) - Top hits data
- radius_ind.csv (optional) - Radius index data

Example usage:
  php bin/console app:create-module data/upload_data/csvs.zip "My Module" "Description of my module" --owner=admin --public
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $zipFile = $input->getArgument('zip-file');
        $moduleName = $input->getArgument('module-name');
        $description = $input->getArgument('description');
        $ownerUsername = $input->getOption('owner');
        $isPublic = $input->getOption('public');
        $force = $input->getOption('force');

        $io->title('Creating Module: ' . $moduleName);

        // Check if ZIP file exists
        if (!file_exists($zipFile)) {
            $io->error("ZIP file not found: {$zipFile}");
            return Command::FAILURE;
        }

        // Extract ZIP file
        $io->section('Extracting ZIP file...');
        $extractedFiles = $this->extractZipFile($zipFile, $io);
        if (empty($extractedFiles)) {
            $io->error('Failed to extract ZIP file or no valid CSV files found');
            return Command::FAILURE;
        }

        // Validate required files
        $io->section('Validating CSV files...');
        $validationResult = $this->validateRequiredFiles($extractedFiles, $io);
        if (!$validationResult && !$force) {
            $io->error('Required CSV files are missing. Use --force to continue anyway.');
            $this->cleanupTempFiles($extractedFiles);
            return Command::FAILURE;
        }

        // Find or create a user for the owner
        $owner = $this->findOrCreateUser($ownerUsername);

        try {
            $io->section('Creating module...');
            
            // Create module using the service with memory management
            $moduleTracking = $this->createModuleWithMemoryManagement(
                $moduleName,
                $description,
                $isPublic,
                $owner,
                $extractedFiles,
                $io
            );

            $io->success([
                'Module created successfully!',
                'Module ID: Module_' . $moduleTracking->getId(),
                'Module Name: ' . $moduleTracking->getName(),
                'Owner: ' . $ownerUsername,
                'Public: ' . ($isPublic ? 'Yes' : 'No')
            ]);

            // Clean up temporary files
            $this->cleanupTempFiles($extractedFiles);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('Module creation failed: ' . $e->getMessage());
            
            // Clean up temporary files on error
            $this->cleanupTempFiles($extractedFiles);
            
            return Command::FAILURE;
        }
    }

    /**
     * Create module with memory management - processes density files one at a time
     */
    private function createModuleWithMemoryManagement(
        string $moduleName,
        string $description,
        bool $isPublic,
        User $owner,
        array $extractedFiles,
        SymfonyStyle $io
    ) {
        // Create module tracking entry first
        $moduleTracking = new ModuleTracking();
        $moduleTracking->setName($moduleName);
        $moduleTracking->setDescription($description);
        $moduleTracking->setPublic($isPublic);
        $moduleTracking->setOwner($owner);

        $this->entityManager->persist($moduleTracking);
        $this->entityManager->flush();

        $moduleId = 'Module_' . $moduleTracking->getId();
        $io->note("Created module tracking entry: {$moduleId}");

        // Create the module database
        $sql = "CREATE DATABASE IF NOT EXISTS `{$moduleId}`";
        $this->entityManager->getConnection()->executeStatement($sql);
        $io->note("Created module database: {$moduleId}");

        // Process regular CSV files using the service
        $io->section('Processing core CSV files...');
        $moduleTracking = $this->moduleCreationService->createModule(
            $moduleName,
            $description,
            $isPublic,
            $owner,
            $extractedFiles['chr.csv'],
            $extractedFiles['chrsupp.csv'],
            $extractedFiles['col.csv'],
            $extractedFiles['ind.csv'],
            $extractedFiles['r_pval.csv'],
            $extractedFiles['r_ratio.csv'],
            $extractedFiles['v_ind.csv'],
            $extractedFiles['row.csv'],
            $extractedFiles['val.csv'],
            [], // Empty density files array - we'll process them separately
            $extractedFiles['radius_ind.csv'] ?? null
        );

        // Process density files one by one for memory efficiency
        if (isset($extractedFiles['density_files']) && !empty($extractedFiles['density_files'])) {
            $io->section('Processing density files (one at a time for memory efficiency)...');
            $densityFiles = $extractedFiles['density_files'];
            
            foreach ($densityFiles as $index => $densityFile) {
                $fileName = $densityFile->getClientOriginalName();
                $io->text("Processing density file " . ($index + 1) . "/" . count($densityFiles) . ": {$fileName}");
                
                // Process this single density file
                $this->processSingleDensityFile($moduleId, $densityFile, $io);
                
                // Force garbage collection after each density file
                gc_collect_cycles();
                $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
                $io->note("Memory usage after {$fileName}: {$memoryUsage}MB");
            }
        }

        return $moduleTracking;
    }

    /**
     * Process a single density file to minimize memory usage
     */
    private function processSingleDensityFile(string $moduleId, UploadedFile $densityFile, SymfonyStyle $io): void
    {
        // Switch to the module database
        $this->entityManager->getConnection()->executeStatement("USE `{$moduleId}`");

        // Create top_hits table if it doesn't exist
        $createTopHitsTable = "CREATE TABLE IF NOT EXISTS `top_hits` (
            `bits` INT(4) NOT NULL,
            `radius_ind` INT(2) UNSIGNED NOT NULL,
            `v_ind` INT(8) UNSIGNED NOT NULL,
            `r_density` INT(4) UNSIGNED NOT NULL,
            `r_naive_p` INT(4) UNSIGNED NOT NULL,
            `left_ind` INT(8) UNSIGNED NOT NULL,
            `right_ind` INT(8) UNSIGNED NOT NULL,
            `left_cnt` INT(4) UNSIGNED NOT NULL,
            `right_cnt` INT(4) UNSIGNED NOT NULL,
            `density` DOUBLE NULL,
            `naive_p` DOUBLE NULL,
            `adj_p` DOUBLE NULL,
            `cal_p` DOUBLE NULL,
            PRIMARY KEY (`bits`, `radius_ind`, `v_ind`),
            INDEX `idx_bits` (`bits`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Top hits data from density analysis'";
        
        $this->entityManager->getConnection()->executeStatement($createTopHitsTable);

        // Extract radius index from filename
        $fileName = $densityFile->getClientOriginalName();
        if (preg_match('/density_(\d+)\.csv$/', $fileName, $matches)) {
            $radiusInd = (int)$matches[1];
            $io->text("Extracted radius_ind: {$radiusInd}");
        } else {
            $io->warning("Could not extract radius index from filename: {$fileName}");
            return;
        }

        // Parse CSV file
        $handle = fopen($densityFile->getPathname(), 'r');
        if (!$handle) {
            $io->error("Could not open density file: {$fileName}");
            return;
        }

        $rowCount = 0;
        $firstRow = true;
        $insertSql = "INSERT INTO `top_hits` (`bits`, `radius_ind`, `v_ind`, `r_density`, `r_naive_p`, `left_ind`, `right_ind`, `left_cnt`, `right_cnt`, `density`, `naive_p`, `adj_p`, `cal_p`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL)";
        $stmt = $this->entityManager->getConnection()->prepare($insertSql);

        while (($row = fgetcsv($handle)) !== false) {
            if ($firstRow) {
                $firstRow = false;
                $io->text("Skipping header row: " . implode(',', $row));
                continue;
            }

            if (count($row) >= 10) {
                $stmt->executeStatement([
                    (int)$row[0],      // bits
                    $radiusInd,        // radius_ind (from filename)
                    (int)$row[1],      // v_ind
                    (int)$row[2],      // r_density
                    (int)$row[3],      // r_naive_p
                    (int)$row[4],      // left_ind
                    (int)$row[5],      // right_ind
                    (int)$row[6],      // left_cnt
                    (int)$row[7],      // right_cnt
                    (float)$row[8],    // density
                    (float)$row[9]     // naive_p
                ]);
                $rowCount++;
            }
        }

        fclose($handle);
        $io->text("Successfully inserted {$rowCount} rows into top_hits table for module: {$moduleId}");
    }

    private function extractZipFile(string $zipPath, SymfonyStyle $io): array
    {
        $tempDir = sys_get_temp_dir() . '/gwatch_module_' . uniqid();
        
        if (!mkdir($tempDir, 0755, true)) {
            $io->error('Failed to create temporary directory');
            return [];
        }

        // Use command-line unzip
        $command = sprintf('unzip -q "%s" -d "%s"', $zipPath, $tempDir);
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $io->error('Failed to extract ZIP file using unzip command');
            $this->removeDirectory($tempDir);
            return [];
        }

        // Find CSV files
        $csvFiles = [];
        $densityFiles = [];
        $radiusIndFile = null;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'csv') {
                $relativePath = str_replace($tempDir . '/', '', $file->getPathname());
                
                // Remove subdirectory prefix if present (e.g., "csvs/chr.csv" -> "chr.csv")
                $fileName = basename($relativePath);
                
                // Check if it's a density file
                if (preg_match('/density_(\d+)\.csv$/', $fileName, $matches)) {
                    $densityFiles[] = $this->createUploadedFile($file->getPathname(), $fileName);
                }
                // Check if it's radius_ind.csv
                elseif ($fileName === 'radius_ind.csv') {
                    $radiusIndFile = $this->createUploadedFile($file->getPathname(), $fileName);
                }
                // Other required files
                else {
                    $csvFiles[$fileName] = $this->createUploadedFile($file->getPathname(), $fileName);
                }
            }
        }

        // Check if we have the required files
        $requiredFiles = ['chr.csv', 'chrsupp.csv', 'col.csv', 'ind.csv', 'r_pval.csv', 'r_ratio.csv', 'v_ind.csv', 'row.csv', 'val.csv'];
        $missingFiles = [];
        
        foreach ($requiredFiles as $requiredFile) {
            if (!isset($csvFiles[$requiredFile])) {
                $missingFiles[] = $requiredFile;
            }
        }

        if (!empty($missingFiles)) {
            $io->warning('Missing required files: ' . implode(', ', $missingFiles));
        }

        if (empty($densityFiles)) {
            $io->warning('No density_X.csv files found');
        }

        $result = array_merge($csvFiles, [
            'density_files' => $densityFiles,
            'radius_ind.csv' => $radiusIndFile,
            '_temp_dir' => $tempDir
        ]);

        return $result;
    }

    private function validateRequiredFiles(array $extractedFiles, SymfonyStyle $io): bool
    {
        $requiredFiles = ['chr.csv', 'chrsupp.csv', 'col.csv', 'ind.csv', 'r_pval.csv', 'r_ratio.csv', 'v_ind.csv', 'row.csv', 'val.csv'];
        $missingFiles = [];
        
        foreach ($requiredFiles as $requiredFile) {
            if (!isset($extractedFiles[$requiredFile])) {
                $missingFiles[] = $requiredFile;
            }
        }

        if (!empty($missingFiles)) {
            $io->warning('Missing required files: ' . implode(', ', $missingFiles));
            return false;
        }

        if (empty($extractedFiles['density_files'])) {
            $io->warning('No density_X.csv files found');
        }

        $io->success('All required CSV files found');
        return true;
    }

    private function createUploadedFile(string $filePath, string $originalName): UploadedFile
    {
        return new UploadedFile(
            $filePath,
            $originalName,
            'text/csv',
            null,
            true
        );
    }

    private function findOrCreateUser(string $username): User
    {
        // Try to find existing user first
        $user = $this->userRepository->findOneBy(['username' => $username]);
        
        if ($user) {
            return $user;
        }
        
        // Create new user if none exists
        $user = new User();
        $user->setUsername($username);
        $user->setMail($username . '@example.com');
        $user->setRole('ROLE_USER');
        $user->setCreatedAt(time());
        
        // Persist the user first
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        return $user;
    }

    private function cleanupTempFiles(array $extractedFiles): void
    {
        if (isset($extractedFiles['_temp_dir']) && is_dir($extractedFiles['_temp_dir'])) {
            $this->removeDirectory($extractedFiles['_temp_dir']);
        }
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }

        rmdir($dir);
    }
}
