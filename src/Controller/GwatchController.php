<?php

namespace App\Controller;

use App\Service\DatabaseManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class GwatchController extends AbstractController
{
    private $databaseManager;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    #[Route('/', name: 'gwatch_home')]
    public function home(): Response
    {
        $response = $this->render('gwatch/home.html.twig');
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->setSharedMaxAge(3600);
        return $response;
    }

    #[Route('/description', name: 'gwatch_description')]
    public function description(): Response
    {
        $response = $this->render('gwatch/description.html.twig');
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->setSharedMaxAge(3600);
        return $response;
    }

    #[Route('/features', name: 'gwatch_features')]
    public function features(): Response
    {
        $response = $this->render('gwatch/features.html.twig');
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->setSharedMaxAge(3600);
        return $response;
    }

    #[Route('/tutorial', name: 'gwatch_tutorial')]
    public function tutorial(): Response
    {
        $response = $this->render('gwatch/tutorial.html.twig');
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->setSharedMaxAge(3600);
        return $response;
    }

    #[Route('/modules', name: 'gwatch_modules')]
    public function modules(): Response
    {
        // Get list of available modules (databases)
        $modules = $this->getAvailableModules();
        
        $response = $this->render('gwatch/modules.html.twig', [
            'modules' => $modules
        ]);
        $response->setPublic();
        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);
        return $response;
    }

    #[Route('/modules/{moduleId}', name: 'gwatch_module_detail')]
    public function moduleDetail(string $moduleId): Response
    {
        // Switch to module database
        if (!$this->databaseManager->switchToModuleDatabase($moduleId)) {
            throw $this->createNotFoundException("Module $moduleId not found");
        }

        // Get module data
        $module = $this->getModuleData($moduleId);
        $tests = $this->getModuleTests($moduleId);

        $response = $this->render('gwatch/module_detail.html.twig', [
            'module' => $module,
            'tests' => $tests
        ]);
        $response->setPublic();
        $response->setMaxAge(300);
        $response->setSharedMaxAge(300);
        return $response;
    }

    #[Route('/modules/{moduleId}/browser', name: 'gwatch_module_browser')]
    public function moduleBrowser(string $moduleId): Response
    {
        if (!$this->databaseManager->switchToModuleDatabase($moduleId)) {
            throw $this->createNotFoundException("Module $moduleId not found");
        }

        $response = $this->render('gwatch/browser.html.twig', [
            'moduleId' => $moduleId
        ]);
        $response->setPublic();
        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);
        return $response;
    }

    #[Route('/modules/{moduleId}/report', name: 'gwatch_module_report', methods: ['GET', 'POST'])]
    public function moduleReport(Request $request, string $moduleId): Response
    {
        if (!$this->databaseManager->switchToModuleDatabase($moduleId)) {
            throw $this->createNotFoundException("Module $moduleId not found");
        }

        if ($request->isMethod('POST')) {
            // Handle report generation
            $reportType = $request->request->get('report_type');
            $windowSize = $request->request->get('window_size');
            $getCsv = $request->request->get('get_csv', false);

            // Generate report logic here
            return $this->json(['status' => 'success', 'message' => 'Report generated']);
        }

        $response = $this->render('gwatch/report.html.twig', [
            'moduleId' => $moduleId
        ]);
        $response->setPublic();
        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);
        return $response;
    }

    /**
     * Get available modules (databases)
     */
    private function getAvailableModules(): array
    {
        return $this->databaseManager->getAvailableModules();
    }

    /**
     * Get module data
     */
    private function getModuleData(string $moduleId): array
    {
        return [
            'id' => $moduleId,
            'name' => "Module $moduleId",
            'description' => "Data for module $moduleId"
        ];
    }

    /**
     * Get module tests
     */
    private function getModuleTests(string $moduleId): array
    {
        // This would query the module's database
        return [
            'Test 1',
            'Test 2', 
            'Test 3'
        ];
    }
} 