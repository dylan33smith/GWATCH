<?php

namespace App\Controller;

use App\Entity\Gwatch\ModuleTracking;
use App\Entity\Gwatch\User;
use App\Repository\ModuleTrackingRepository;
use App\Repository\UserRepository;
use App\Service\TopHitsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\DriverManager;

class GwatchController extends AbstractController
{
    private $params;
    private $entityManager;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $entityManager)
    {
        $this->params = $params;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'gwatch_home')]
    public function home(SessionInterface $session): Response
    {
        // Clear any old flash messages when accessing home page
        // This prevents messages from previous user sessions from appearing
        if (!$session->has('user_id')) {
            $session->getFlashBag()->clear();
        }
        
        return $this->render('gwatch/home.html.twig');
    }

    #[Route('/description', name: 'gwatch_description')]
    public function description(): Response
    {
        return $this->render('gwatch/description.html.twig');
    }

    #[Route('/features', name: 'gwatch_features')]
    public function features(): Response
    {
        return $this->render('gwatch/features.html.twig');
    }

    #[Route('/tutorial', name: 'gwatch_tutorial')]
    public function tutorial(): Response
    {
        return $this->render('gwatch/tutorial.html.twig');
    }

    /**
     * Display datasets page with user-specific and public modules
     * 
     * @param ModuleTrackingRepository $moduleTrackingRepository Repository for module operations
     * @param UserRepository $userRepository Repository for user operations
     * @param SessionInterface $session User session for authentication
     * @return Response Rendered datasets page
     */
    #[Route('/datasets', name: 'gwatch_datasets')]
    public function datasets(ModuleTrackingRepository $moduleTrackingRepository, UserRepository $userRepository, SessionInterface $session): Response
    {
        // Check if user is logged in
        $isLoggedIn = $session->has('user_id');
        $currentUser = null;
        $userModules = [];
        
        if ($isLoggedIn) {
            $currentUser = $userRepository->find($session->get('user_id'));
            if ($currentUser) {
                // Fetch modules owned by the current user
                $userModules = $this->fetchUserModules($moduleTrackingRepository, $currentUser);
            }
        }
        
        // Fetch all public modules, excluding those owned by the current user
        $publicModules = $this->fetchPublicModules($moduleTrackingRepository, $isLoggedIn, $currentUser);

        return $this->render('gwatch/datasets.html.twig', [
            'isLoggedIn' => $isLoggedIn,
            'currentUser' => $currentUser,
            'userModules' => $userModules,
            'publicModules' => $publicModules,
        ]);
    }

    /**
     * Display top hits report page for a specific module
     * 
     * @param int $moduleId The module ID to display report for
     * @param int $topHitsCount The number of top hits to display
     * @param string $reportType The type of report (P-value, QAS, density)
     * @param ModuleTrackingRepository $moduleTrackingRepository Repository for module operations
     * @return Response Rendered top hits report page
     */
    #[Route('/top-hits-report/{moduleId}/{topHitsCount}/{reportType}', name: 'gwatch_top_hits_report', requirements: ['topHitsCount' => '\d+', 'reportType' => '.+'])]
    public function topHitsReport(int $moduleId, int $topHitsCount, string $reportType, ModuleTrackingRepository $moduleTrackingRepository): Response
    {
        // Fetch module information
        $module = $moduleTrackingRepository->find($moduleId);
        
        if (!$module) {
            throw $this->createNotFoundException('Module not found');
        }
        
        // Get module name for display
        $moduleName = $module->getName();
        
        // Decode the URL-encoded report type
        $decodedReportType = urldecode($reportType);
        
        // Get top hits data using the service (without window size)
        $topHitsService = new TopHitsService($this->params);
        
        $topHitsData = $topHitsService->generateHybridTopHitsReport(
            $moduleId, 
            $decodedReportType, 
            $topHitsCount
        );
        
        return $this->render('gwatch/top_hits_report.html.twig', [
            'moduleId' => $moduleId,
            'moduleName' => $moduleName,
            'topHitsCount' => $topHitsCount,
            'reportType' => $decodedReportType,
            'topHitsData' => $topHitsData,
        ]);
    }

    /**
     * Fetch chromosome data for a specific module
     * 
     * @param int $moduleId The module ID to fetch chromosome data for
     * @return JsonResponse JSON response containing chromosome data
     */
    #[Route('/api/module/{moduleId}/chromosomes', name: 'gwatch_module_chromosomes', methods: ['GET'])]
    public function getModuleChromosomes(int $moduleId): JsonResponse
    {
        try {
            // Create connection to the module database
            $dbName = "Module_{$moduleId}";
            $connection = $this->createModuleConnection($dbName);
            
            if (!$connection) {
                return new JsonResponse([
                    'error' => 'Module database not found',
                    'debug' => [
                        'moduleId' => $moduleId,
                        'database' => $dbName,
                        'connectionFailed' => true
                    ]
                ], 404);
            }
            
            // First check if the chr table exists and has data
            $checkTableSql = "SHOW TABLES LIKE 'chr'";
            $checkStmt = $connection->prepare($checkTableSql);
            $result = $checkStmt->executeQuery();
            $tableExists = $result->fetchAllAssociative();
            
            if (empty($tableExists)) {
                $connection->close();
                return new JsonResponse([
                    'error' => 'Chr table not found in module database',
                    'debug' => [
                        'moduleId' => $moduleId,
                        'database' => $dbName,
                        'tableExists' => false,
                        'availableTables' => $this->getAvailableTables($connection)
                    ]
                ], 404);
            }
            
            // Check if table has data
            $countSql = "SELECT COUNT(*) as count FROM chr";
            $countStmt = $connection->prepare($countSql);
            $result = $countStmt->executeQuery();
            $countResult = $result->fetchAssociative();
            $rowCount = $countResult['count'] ?? 0;
            
            if ($rowCount == 0) {
                $connection->close();
                return new JsonResponse([
                    'error' => 'Chr table is empty',
                    'debug' => [
                        'moduleId' => $moduleId,
                        'database' => $dbName,
                        'tableExists' => true,
                        'rowCount' => $rowCount
                    ]
                ], 404);
            }
            
            // Query chromosome data from the chr table
            $sql = "SELECT chr, chrname, len FROM chr ORDER BY chr ASC";
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery();
            $chromosomes = $result->fetchAllAssociative();
            
            $connection->close();
            
            return new JsonResponse([
                'success' => true,
                'data' => $chromosomes,
                'debug' => [
                    'moduleId' => $moduleId,
                    'database' => $dbName,
                    'tableExists' => !empty($tableExists),
                    'rowCount' => $rowCount,
                    'fetchedRows' => count($chromosomes)
                ]
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to fetch chromosome data',
                'message' => $e->getMessage(),
                'debug' => [
                    'moduleId' => $moduleId,
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }
    
    /**
     * Create a direct connection to a module database
     */
    private function createModuleConnection(string $dbName)
    {
        try {
            $baseUrl = $this->params->get('app.database_url');
            $urlParts = parse_url($baseUrl);
            $urlParts['path'] = '/' . $dbName;
            
            $moduleUrl = $this->buildUrl($urlParts);
            return DriverManager::getConnection(['url' => $moduleUrl]);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Build URL from parts
     */
    private function buildUrl(array $parts): string
    {
        $url = '';
        
        if (isset($parts['scheme'])) {
            $url .= $parts['scheme'] . '://';
        }
        
        if (isset($parts['user'])) {
            $url .= $parts['user'];
            if (isset($parts['pass'])) {
                $url .= ':' . $parts['pass'];
            }
            $url .= '@';
        }
        
        if (isset($parts['host'])) {
            $url .= $parts['host'];
            if (isset($parts['port'])) {
                $url .= ':' . $parts['port'];
            }
        }
        
        if (isset($parts['path'])) {
            $url .= $parts['path'];
        }
        
        if (isset($parts['query'])) {
            $url .= '?' . $parts['query'];
        }
        
        return $url;
    }

    /**
     * Get available tables in the module database for debugging
     */
    private function getAvailableTables($connection): array
    {
        try {
            $sql = "SHOW TABLES";
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery();
            $tables = $result->fetchAllAssociative();
            
            $tableNames = [];
            foreach ($tables as $table) {
                $tableNames[] = $table['Tables_in_' . strtolower($connection->getDatabase())] ?? $table[0] ?? '';
            }
            
            return $tableNames;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Check if a manhattan plot exists for a specific test
     */
    #[Route('/api/module/{moduleId}/manhattan-plot-exists/{testNumber}', name: 'gwatch_manhattan_plot_exists', methods: ['GET'])]
    public function checkManhattanPlotExists(int $moduleId, int $testNumber): JsonResponse
    {
        try {
            $dbName = "Module_{$moduleId}";
            $connection = $this->createModuleConnection($dbName);
            
            if (!$connection) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Could not connect to module database',
                    'debug' => ['moduleId' => $moduleId, 'database' => $dbName]
                ], 404);
            }
            
            // Check if mplot_png table exists and has data for this test
            $sql = "SELECT COUNT(*) as count FROM mplot_png WHERE test_number = ?";
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery([$testNumber]);
            $row = $result->fetchAssociative();
            
            $connection->close();
            
            $exists = $row && $row['count'] > 0;
            
            return new JsonResponse([
                'success' => true,
                'exists' => $exists,
                'testNumber' => $testNumber,
                'moduleId' => $moduleId
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Failed to check manhattan plot existence',
                'message' => $e->getMessage(),
                'debug' => [
                    'moduleId' => $moduleId,
                    'testNumber' => $testNumber,
                    'exception' => get_class($e)
                ]
            ], 500);
        }
    }

    /**
     * Test endpoint to check available modules and their databases
     */
    #[Route('/api/debug/modules', name: 'gwatch_debug_modules', methods: ['GET'])]
    public function debugModules(): JsonResponse
    {
        try {
            // Get all modules from the tracking table
            $connection = $this->entityManager->getConnection();
            $sql = "SELECT id, name, public FROM module_tracking ORDER BY id ASC";
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery();
            $modules = $result->fetchAllAssociative();
            
            $moduleInfo = [];
            foreach ($modules as $module) {
                $moduleId = $module['id'];
                $dbName = "Module_{$moduleId}";
                
                // Try to connect to the module database
                $moduleConnection = $this->createModuleConnection($dbName);
                $hasDatabase = $moduleConnection !== null;
                
                if ($hasDatabase) {
                    $tables = $this->getAvailableTables($moduleConnection);
                    $moduleConnection->close();
                } else {
                    $tables = [];
                }
                
                $moduleInfo[] = [
                    'id' => $moduleId,
                    'name' => $module['name'],
                    'public' => (bool)$module['public'],
                    'chrData' => $this->getChrDataSample($dbName),
                    'database' => $dbName,
                    'hasDatabase' => $hasDatabase,
                    'tables' => $tables
                ];
            }
            
            return new JsonResponse([
                'success' => true,
                'modules' => $moduleInfo,
                'totalModules' => count($modules),
                'databaseUrl' => $this->params->get('app.database_url')
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to debug modules',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    /**
     * Get a sample of chromosome data from a module database
     */
    private function getChrDataSample(string $dbName): array
    {
        try {
            $connection = $this->createModuleConnection($dbName);
            if (!$connection) {
                return ['error' => 'Cannot connect to database'];
            }
            
            $sql = "SELECT chr, chrname, len FROM chr LIMIT 3";
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery();
            $data = $result->fetchAllAssociative();
            
            $connection->close();
            return $data;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Fetch modules owned by the current user
     */
    private function fetchUserModules(ModuleTrackingRepository $moduleTrackingRepository, User $currentUser): array
    {
        return $moduleTrackingRepository->createQueryBuilder('m')
            ->where('m.owner = :owner')
            ->setParameter('owner', $currentUser)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Fetch public modules, excluding those owned by the current user
     */
    private function fetchPublicModules(ModuleTrackingRepository $moduleTrackingRepository, bool $isLoggedIn, ?User $currentUser): array
    {
        $publicModulesQuery = $moduleTrackingRepository->createQueryBuilder('m')
            ->where('m.public = :public')
            ->setParameter('public', true);
            
        if ($isLoggedIn && $currentUser) {
            $publicModulesQuery->andWhere('m.owner != :currentUser')
                ->setParameter('currentUser', $currentUser);
        }
        
        return $publicModulesQuery
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Fetch column data for a specific module
     * 
     * @param int $moduleId The module ID to fetch column data for
     * @return JsonResponse JSON response containing column data
     */
    #[Route('/api/module/{moduleId}/columns', name: 'gwatch_module_columns', methods: ['GET'])]
    public function getModuleColumns(int $moduleId): JsonResponse
    {
        try {
            // Create connection to the module database
            $dbName = "Module_{$moduleId}";
            $connection = $this->createModuleConnection($dbName);
            
            if (!$connection) {
                return new JsonResponse([
                    'error' => 'Module database not found',
                    'debug' => [
                        'moduleId' => $moduleId,
                        'database' => $dbName,
                        'connectionFailed' => true
                    ]
                ], 404);
            }
            
            // First check if the col table exists and has data
            $checkTableSql = "SHOW TABLES LIKE 'col'";
            $checkStmt = $connection->prepare($checkTableSql);
            $result = $checkStmt->executeQuery();
            $tableExists = $result->fetchAllAssociative();
            
            if (empty($tableExists)) {
                $connection->close();
                return new JsonResponse([
                    'error' => 'Col table not found in module database',
                    'debug' => [
                        'moduleId' => $moduleId,
                        'database' => $dbName,
                        'tableExists' => false,
                        'availableTables' => $this->getAvailableTables($connection)
                    ]
                ], 404);
            }
            
            // Check if table has data
            $countSql = "SELECT COUNT(*) as count FROM col";
            $countStmt = $connection->prepare($countSql);
            $result = $countStmt->executeQuery();
            $countResult = $result->fetchAssociative();
            $rowCount = $countResult['count'] ?? 0;
            
            if ($rowCount == 0) {
                $connection->close();
                return new JsonResponse([
                    'error' => 'Col table is empty',
                    'debug' => [
                        'moduleId' => $moduleId,
                        'database' => $dbName,
                        'tableExists' => true,
                        'rowCount' => $rowCount
                    ]
                ], 404);
            }
            
            // Query column data from the col table
            $sql = "SELECT col, test FROM col ORDER BY col ASC";
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery();
            $columns = $result->fetchAllAssociative();
            
            $connection->close();
            
            return new JsonResponse([
                'success' => true,
                'data' => $columns,
                'debug' => [
                    'moduleId' => $moduleId,
                    'database' => $dbName,
                    'tableExists' => !empty($tableExists),
                    'rowCount' => $rowCount,
                    'fetchedRows' => count($columns)
                ]
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to fetch column data',
                'message' => $e->getMessage(),
                'debug' => [
                    'moduleId' => $moduleId,
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }



    /**
     * Generate and download top hits data as CSV
     */
    #[Route('/api/module/{moduleId}/top-hits-csv', name: 'gwatch_top_hits_csv', methods: ['GET'])]
    public function getTopHitsCsv(int $moduleId, Request $request): Response
    {
        try {
            $count = $request->query->get('count', 1000);
            $type = $request->query->get('type', 'P-value');
            
            // Use the TopHitsService to get the data
            $topHitsService = new TopHitsService($this->params);
            $result = $topHitsService->generateHybridTopHitsReport($moduleId, $type, $count);
            
            if (!$result['success']) {
                return new Response('Error: ' . ($result['error'] ?? 'Unknown error'), 500);
            }
            
            $data = $result['data'];
            if (empty($data)) {
                return new Response('No data available', 404);
            }
            
            // Generate CSV content
            $csvContent = $this->generateCsvContent($data);
            
            // Create response with CSV headers
            $response = new Response($csvContent);
            $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment; filename="top-hits-' . $type . '-' . $moduleId . '-' . $count . '.csv"');
            
            return $response;
            
        } catch (\Exception $e) {
            return new Response('Error generating CSV: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Generate CSV content from top hits data
     */
    private function generateCsvContent(array $data): string
    {
        if (empty($data)) {
            return '';
        }
        
        // CSV headers (matching the table columns exactly)
        $headers = [
            'SNP',
            'Chromosome', 
            'position',
            'test_name',
            '-log(p)',
            'ranks_of_-log(p)',
            'QAS',
            'ranks_of_QAS',
            'rank_of_density',
            'rank of naive p-value',
            'left_ind',
            'right_ind',
            'number of SNPs',
            'density',
            '-log(naive p-value)'
        ];
        
        // Start with headers
        $csv = implode(',', array_map('self::escapeCsvField', $headers)) . "\n";
        
        // Add data rows
        foreach ($data as $row) {
            $csvRow = [
                $row['SNP'] ?? '',
                $row['Chromosome'] ?? '',
                $row['position'] ?? '',
                $row['test_name'] ?? '',
                $row['-log(p)'] ?? '',
                $row['ranks_of_-log(p)'] ?? '',
                $row['QAS'] ?? '',
                $row['ranks_of_QAS'] ?? '',
                $row['rank_of_density'] ?? '',
                $row['rank_of_naive_p_value'] ?? '',
                $row['left_ind'] ?? '',
                $row['right_ind'] ?? '',
                $row['number_of_SNPs'] ?? '',
                $row['density'] ?? '',
                $row['-log(naive_p_value)'] ?? ''
            ];
            
            $csv .= implode(',', array_map('self::escapeCsvField', $csvRow)) . "\n";
        }
        
        return $csv;
    }
    
    /**
     * Escape CSV field value
     */
    private static function escapeCsvField($value): string
    {
        if (is_null($value)) {
            return '';
        }
        
        $value = (string)$value;
        
        // If field contains comma, quote, or newline, wrap in quotes and escape internal quotes
        if (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false) {
            $value = '"' . str_replace('"', '""', $value) . '"';
        }
        
        return $value;
    }

    /**
     * Simple test endpoint to check if the API is working
     */
    #[Route('/api/test', name: 'gwatch_test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'API is working',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
} 