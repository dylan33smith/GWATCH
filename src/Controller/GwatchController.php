<?php

namespace App\Controller;

use App\Entity\Gwatch\ModuleTracking;
use App\Entity\Gwatch\User;
use App\Repository\ModuleTrackingRepository;
use App\Repository\UserRepository;
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
     * Fetch radius values from the radius_ind table for a specific module
     * 
     * @param int $moduleId The module ID to fetch radius values for
     * @return JsonResponse JSON response containing radius values
     */
    #[Route('/api/module/{moduleId}/radius', name: 'gwatch_module_radius', methods: ['GET'])]
    public function getModuleRadius(int $moduleId): JsonResponse
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
            
            // First check if the radius_ind table exists
            $checkTableSql = "SHOW TABLES LIKE 'radius_ind'";
            $checkStmt = $connection->prepare($checkTableSql);
            $result = $checkStmt->executeQuery();
            $tableExists = $result->fetchAllAssociative();
            
            if (empty($tableExists)) {
                $connection->close();
                return new JsonResponse([
                    'error' => 'Radius_ind table not found in module database',
                    'debug' => [
                        'moduleId' => $moduleId,
                        'database' => $dbName,
                        'tableExists' => false,
                        'availableTables' => $this->getAvailableTables($connection)
                    ]
                ], 404);
            }
            
            // Query radius values from the radius_ind table, ordered by radius_type and radius_val
            $sql = "SELECT radius_ind as id, radius_type as type, radius_val FROM radius_ind ORDER BY radius_type ASC, radius_val ASC";
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery();
            $radiusValues = $result->fetchAllAssociative();
            
            $connection->close();
            
            return new JsonResponse([
                'success' => true,
                'data' => $radiusValues,
                'debug' => [
                    'moduleId' => $moduleId,
                    'database' => $dbName,
                    'tableExists' => !empty($tableExists),
                    'fetchedRows' => count($radiusValues)
                ]
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to fetch radius values',
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