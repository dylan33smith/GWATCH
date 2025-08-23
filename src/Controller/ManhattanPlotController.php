<?php

namespace App\Controller;

use App\Service\ManhattanPlotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ManhattanPlotController extends AbstractController
{
    /**
     * Display a manhattan plot for a specific test
     */
    #[Route("/manhattan-plot/{moduleId}/{testNumber}", name: "app_manhattan_plot")]
    public function displayManhattanPlot(
        int $moduleId, 
        int $testNumber, 
        Request $request,
        SessionInterface $session,
        ManhattanPlotService $manhattanPlotService
    ): Response {
        // Check if user is logged in
        if (!$session->has('user_id')) {
            $this->addFlash('error', 'Please login to view manhattan plots.');
            return $this->redirectToRoute('app_login');
        }

        try {
            // Get the PNG data
            $pngData = $manhattanPlotService->getManhattanPlotPng($moduleId, $testNumber);
            
            if (!$pngData) {
                throw new \Exception('Manhattan plot not found for this test.');
            }

            // Get the metadata for significant SNPs
            $metadata = $manhattanPlotService->getManhattanPlotMetadata($moduleId, $testNumber);

            // Convert PNG data to base64 for display
            $pngBase64 = base64_encode($pngData);

            return $this->render('manhattan_plot/display.html.twig', [
                'moduleId' => $moduleId,
                'testNumber' => $testNumber,
                'pngBase64' => $pngBase64,
                'metadata' => $metadata,
                'username' => $session->get('username'),
            ]);

        } catch (\Exception $e) {
            $this->addFlash('error', 'Error loading manhattan plot: ' . $e->getMessage());
            return $this->redirectToRoute('gwatch_home');
        }
    }

    /**
     * Serve PNG image directly (for debugging or external use)
     */
    #[Route("/manhattan-plot-png/{moduleId}/{testNumber}", name: "app_manhattan_plot_png")]
    public function serveManhattanPlotPng(
        int $moduleId, 
        int $testNumber, 
        SessionInterface $session,
        ManhattanPlotService $manhattanPlotService
    ): Response {
        // Check if user is logged in
        if (!$session->has('user_id')) {
            return new Response('Unauthorized', 401);
        }

        try {
            $pngData = $manhattanPlotService->getManhattanPlotPng($moduleId, $testNumber);
            
            if (!$pngData) {
                return new Response('Plot not found', 404);
            }

            return new Response($pngData, 200, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'public, max-age=3600'
            ]);

        } catch (\Exception $e) {
            return new Response('Error loading plot', 500);
        }
    }
}
