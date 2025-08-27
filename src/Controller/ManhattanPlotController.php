<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Controller for handling Manhattan plot display and interactions
 */
class ManhattanPlotController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Display Manhattan plot for a specific test
     */
    #[Route('/module/{moduleId}/test/{testId}/manhattan-plot', name: 'app_manhattan_plot')]
    public function showManhattanPlot(int $moduleId, int $testId): Response
    {
        try {
            // Get the module database connection
            $connection = $this->entityManager->getConnection();
            $moduleDbName = 'Module_' . $moduleId;
            
            // Switch to the module database
            $connection->executeStatement("USE `{$moduleDbName}`");
            
            // Get the plot data
            $plotQuery = "SELECT test_name, plot_image, plot_width, plot_height FROM mplots WHERE test_id = :test_id";
            $stmt = $connection->prepare($plotQuery);
            $stmt->bindValue('test_id', $testId);
            $result = $stmt->executeQuery();
            $plotData = $result->fetchAssociative();
            
            if (!$plotData) {
                throw new \Exception("Manhattan plot not found for test ID {$testId}");
            }
            
            // Get significant points for this test
            $pointsQuery = "SELECT snp_ind, x_pixel, y_pixel, p_value, neg_log_p, chromosome FROM significant_points WHERE test_id = :test_id ORDER BY neg_log_p DESC";
            $stmt = $connection->prepare($pointsQuery);
            $stmt->bindValue('test_id', $testId);
            $result = $stmt->executeQuery();
            $significantPoints = $result->fetchAllAssociative();
            
            // Convert plot image to base64 for display
            $plotImageBase64 = base64_encode($plotData['plot_image']);
            
            return $this->render('gwatch/manhattan_plot.html.twig', [
                'moduleId' => $moduleId,
                'testId' => $testId,
                'testName' => $plotData['test_name'],
                'plotImage' => $plotImageBase64,
                'plotWidth' => $plotData['plot_width'],
                'plotHeight' => $plotData['plot_height'],
                'significantPoints' => $significantPoints
            ]);
            
        } catch (\Exception $e) {
            // Log the error
            error_log("Error displaying Manhattan plot for module {$moduleId}, test {$testId}: " . $e->getMessage());
            
            // Return an error response
            return $this->render('gwatch/manhattan_plot_error.html.twig', [
                'error' => 'Unable to load Manhattan plot. Please try again later.',
                'moduleId' => $moduleId,
                'testId' => $testId
            ]);
        }
    }

    /**
     * Handle click on significant point - redirect to placeholder page
     */
    #[Route('/module/{moduleId}/snp/{snpInd}', name: 'app_snp_details')]
    public function showSnpDetails(int $moduleId, int $snpInd): Response
    {
        // For now, just show a placeholder page
        return $this->render('gwatch/snp_details_placeholder.html.twig', [
            'moduleId' => $moduleId,
            'snpInd' => $snpInd,
            'message' => 'SNP details page is still being built. This will show detailed information about the selected SNP.'
        ]);
    }

    /**
     * API endpoint to get significant points data for a test
     */
    #[Route('/api/module/{moduleId}/test/{testId}/significant-points', name: 'api_significant_points', methods: ['GET'])]
    public function getSignificantPoints(int $moduleId, int $testId): Response
    {
        try {
            $connection = $this->entityManager->getConnection();
            $moduleDbName = 'Module_' . $moduleId;
            
            // Switch to the module database
            $connection->executeStatement("USE `{$moduleDbName}`");
            
            // Get significant points
            $query = "SELECT snp_ind, x_pixel, y_pixel, p_value, neg_log_p, chromosome FROM significant_points WHERE test_id = :test_id ORDER BY neg_log_p DESC";
            $stmt = $connection->prepare($query);
            $stmt->bindValue('test_id', $testId);
            $result = $stmt->executeQuery();
            $points = $result->fetchAllAssociative();
            
            return $this->json([
                'success' => true,
                'data' => $points
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
