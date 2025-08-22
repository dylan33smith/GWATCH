<?php

namespace App\Controller;

use App\Entity\Gwatch\ModuleTracking;
use App\Entity\Gwatch\User;
use App\Form\DataUploadType;
use App\Repository\UserRepository;
use App\Service\ModuleCreationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{
    /**
     * Handle data upload form submission and module creation
     * 
     * @param Request $request The HTTP request
     * @param SessionInterface $session User session for authentication
     * @param UserRepository $userRepository Repository for user operations
     * @param ModuleCreationService $moduleCreationService Service for creating modules
     * @return Response Rendered upload page or redirect
     */
    #[Route('/upload', name: 'app_upload')]
    public function upload(
        Request $request, 
        SessionInterface $session, 
        UserRepository $userRepository,
        ModuleCreationService $moduleCreationService
    ): Response {
        // Check if user is logged in
        if (!$session->has('user_id')) {
            $this->addFlash('error', 'Please login to access the upload page.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(DataUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $currentUser = $userRepository->find($session->get('user_id'));
            
            if (!$currentUser) {
                $this->addFlash('error', 'User session expired. Please login again.');
                return $this->redirectToRoute('app_login');
            }
            
            try {
                // Process the ZIP file
                $this->addFlash('info', 'Processing ZIP file...');
                
                $extractedFiles = $this->processZipFile($data['csvZipFile']);
                
                if (empty($extractedFiles)) {
                    throw new \Exception('No valid CSV files found in the ZIP file.');
                }
                
                // Validate required files
                $this->validateRequiredFiles($extractedFiles);
                
                $this->addFlash('info', 'ZIP file processed successfully. Starting module creation...');
                
                // Create module using the service
                $moduleTracking = $moduleCreationService->createModule(
                    $data['moduleName'],
                    $data['description'],
                    $data['makePublic'] ?? false,
                    $currentUser,
                    $extractedFiles['chr.csv'],
                    $extractedFiles['chrsupp.csv'],
                    $extractedFiles['col.csv'],
                    $extractedFiles['ind.csv'],
                    $extractedFiles['r_pval.csv'],
                    $extractedFiles['r_ratio.csv'],
                    $extractedFiles['v_ind.csv'],
                    $extractedFiles['row.csv'],
                    $extractedFiles['val.csv'],
                    $extractedFiles['density_files'],
                    $extractedFiles['radius_ind.csv'] ?? null
                );
                
                $this->addFlash('upload_success', 'Module "' . $data['moduleName'] . '" created successfully! Module ID: Module_' . $moduleTracking->getId());
                
                // Clean up temporary files
                $this->cleanupTempFiles($extractedFiles);
                
                return $this->redirectToRoute('app_upload');
                
            } catch (\Exception $e) {
                // Clean up temporary files on error
                if (isset($extractedFiles)) {
                    $this->cleanupTempFiles($extractedFiles);
                }
                
                // Log technical error for debugging
                error_log('Module creation error: ' . $e->getMessage());
                
                // Show user-friendly error message
                $this->addFlash('error', 'Upload failed: ' . $e->getMessage());
            }
        }

        return $this->render('upload/upload.html.twig', [
            'form' => $form->createView(),
            'username' => $session->get('username'),
        ]);
    }
    
    /**
     * Process ZIP file and extract CSV files
     */
    private function processZipFile(UploadedFile $zipFile): array
    {
        $tempDir = sys_get_temp_dir() . '/gwatch_upload_' . uniqid();
        if (!mkdir($tempDir, 0755, true)) {
            throw new \Exception('Could not create temporary directory.');
        }
        
        $zipPath = $zipFile->getPathname();
        
        // Try to use ZipArchive if available
        if (class_exists('ZipArchive')) {
            return $this->extractWithZipArchive($zipPath, $tempDir);
        }
        
        // Fallback: try to use system unzip command
        if ($this->hasSystemUnzip()) {
            return $this->extractWithSystemUnzip($zipPath, $tempDir);
        }
        
        // Last resort: try to read ZIP manually
        return $this->extractZipManually($zipPath, $tempDir);
    }
    
    /**
     * Extract using PHP's ZipArchive class
     */
    private function extractWithZipArchive(string $zipPath, string $tempDir): array
    {
        $zip = new \ZipArchive();
        
        if ($zip->open($zipPath) !== true) {
            throw new \Exception('Could not open ZIP file.');
        }
        
        $extractedFiles = [];
        $densityFiles = [];
        
        // Extract files
        $zip->extractTo($tempDir);
        $zip->close();
        
        // Process extracted files
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($files as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'csv') {
                $fileName = strtolower($file->getBasename('.csv'));
                
                // Create a temporary UploadedFile-like object
                $tempFile = $this->createTempUploadedFile($file->getPathname(), $file->getBasename());
                
                // Categorize files
                if (preg_match('/^density_\d+$/', $fileName)) {
                    $densityFiles[] = $tempFile;
                } elseif (in_array($fileName, ['chr', 'chrsupp', 'col', 'ind', 'r_pval', 'r_ratio', 'v_ind', 'row', 'val', 'radius_ind'])) {
                    $extractedFiles[$fileName . '.csv'] = $tempFile;
                }
            }
        }
        
        $extractedFiles['density_files'] = $densityFiles;
        $extractedFiles['temp_dir'] = $tempDir;
        
        return $extractedFiles;
    }
    
    /**
     * Extract using system unzip command
     */
    private function extractWithSystemUnzip(string $zipPath, string $tempDir): array
    {
        $command = "unzip -q '{$zipPath}' -d '{$tempDir}' 2>&1";
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Failed to extract ZIP file using system unzip command.');
        }
        
        return $this->processExtractedFiles($tempDir);
    }
    
    /**
     * Manual ZIP extraction (basic implementation)
     */
    private function extractZipManually(string $zipPath, string $tempDir): array
    {
        // This is a very basic implementation and may not work with all ZIP files
        // In production, you should ensure ZipArchive or system unzip is available
        
        $zipContent = file_get_contents($zipPath);
        if ($zipContent === false) {
            throw new \Exception('Could not read ZIP file.');
        }
        
        // Look for CSV files in the ZIP content (very basic approach)
        $csvFiles = [];
        $densityFiles = [];
        
        // This is a simplified approach - in reality, you'd need proper ZIP parsing
        // For now, we'll throw an exception suggesting to install proper ZIP support
        throw new \Exception('ZIP processing not available. Please ensure PHP ZipArchive extension or system unzip command is available.');
        
        return $this->processExtractedFiles($tempDir);
    }
    
    /**
     * Process extracted files and categorize them
     */
    private function processExtractedFiles(string $tempDir): array
    {
        $extractedFiles = [];
        $densityFiles = [];
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($files as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'csv') {
                $fileName = strtolower($file->getBasename('.csv'));
                
                // Create a temporary UploadedFile-like object
                $tempFile = $this->createTempUploadedFile($file->getPathname(), $file->getBasename());
                
                // Categorize files
                if (preg_match('/^density_\d+$/', $fileName)) {
                    $densityFiles[] = $tempFile;
                } elseif (in_array($fileName, ['chr', 'chrsupp', 'col', 'ind', 'r_pval', 'r_ratio', 'v_ind', 'row', 'val', 'radius_ind'])) {
                    $extractedFiles[$fileName . '.csv'] = $tempFile;
                }
            }
        }
        
        $extractedFiles['density_files'] = $densityFiles;
        $extractedFiles['temp_dir'] = $tempDir;
        
        return $extractedFiles;
    }
    
    /**
     * Check if system unzip command is available
     */
    private function hasSystemUnzip(): bool
    {
        $output = [];
        $returnCode = 0;
        exec('which unzip', $output, $returnCode);
        return $returnCode === 0;
    }
    
    /**
     * Validate that all required files are present
     */
    private function validateRequiredFiles(array $extractedFiles): void
    {
        $requiredFiles = [
            'chr.csv', 'chrsupp.csv', 'col.csv', 'ind.csv', 
            'r_pval.csv', 'r_ratio.csv', 'v_ind.csv', 'row.csv', 'val.csv'
        ];
        
        $missingFiles = [];
        foreach ($requiredFiles as $requiredFile) {
            if (!isset($extractedFiles[$requiredFile])) {
                $missingFiles[] = $requiredFile;
            }
        }
        
        if (!empty($missingFiles)) {
            throw new \Exception('Missing required files: ' . implode(', ', $missingFiles));
        }
        
        if (empty($extractedFiles['density_files'])) {
            throw new \Exception('At least one density_#.csv file is required.');
        }
        
        $this->addFlash('info', 'Found ' . count($extractedFiles['density_files']) . ' density files and all required CSV files.');
    }
    
    /**
     * Create a temporary UploadedFile-like object for processing
     */
    private function createTempUploadedFile(string $path, string $originalName): UploadedFile
    {
        // Create a temporary copy of the file with a proper name
        $tempPath = tempnam(sys_get_temp_dir(), 'gwatch_csv_');
        copy($path, $tempPath);
        
        // Create a proper UploadedFile object
        return new UploadedFile(
            $tempPath,
            $originalName,
            mime_content_type($path) ?: 'text/csv',
            UPLOAD_ERR_OK,
            true // Move the file instead of copying
        );
    }
    
    /**
     * Clean up temporary files and directories
     */
    private function cleanupTempFiles(array $extractedFiles): void
    {
        // Clean up any temporary files that weren't moved by UploadedFile
        foreach ($extractedFiles as $key => $value) {
            if ($key === 'temp_dir' || $key === 'density_files') {
                continue; // Skip non-file entries
            }
            
            if ($value instanceof UploadedFile) {
                $path = $value->getPathname();
                if (file_exists($path) && strpos($path, sys_get_temp_dir()) === 0) {
                    @unlink($path);
                }
            }
        }
        
        // Clean up the temporary directory
        if (isset($extractedFiles['temp_dir']) && is_dir($extractedFiles['temp_dir'])) {
            $this->removeDirectory($extractedFiles['temp_dir']);
        }
    }
    
    /**
     * Recursively remove a directory and its contents
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        
        rmdir($dir);
    }
}
