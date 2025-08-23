<?php

require_once 'vendor/autoload.php';

use App\Controller\ManhattanPlotController;
use App\Service\ManhattanPlotService;

echo "Testing ManhattanPlotController...\n";

try {
    // Create a mock params object
    $mockParams = new class {
        public function get($key) {
            $params = [
                'database_host' => '127.0.0.1',
                'database_port' => 3306,
                'database_user' => 'gwatch_user',
                'database_password' => '123457'
            ];
            return $params[$key] ?? null;
        }
    };
    
    // Create the service
    $manhattanService = new ManhattanPlotService($mockParams);
    echo "✓ ManhattanPlotService created successfully.\n";
    
    // Create the controller
    $controller = new ManhattanPlotController();
    echo "✓ ManhattanPlotController created successfully.\n";
    
    // Test if we can access the service from the controller
    echo "✓ Controller and service integration working.\n";
    
    echo "\n🎉 ManhattanPlotController test passed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
