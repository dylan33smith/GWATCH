<?php
// getColumns.php - API endpoint for highway browser column data
// Routes to DisplayController::columnsAction()

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\DisplayController;
use Doctrine\ORM\EntityManagerInterface;

// Create a simple request object from GET parameters
$request = Request::createFromGlobals();

// For now, we'll create a minimal response that matches what the JavaScript expects
// This is a temporary solution until we can properly integrate with Symfony routing

$module = $request->query->get('module');
$chr = $request->query->get('chr');

if (!$module || !$chr) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing module or chr parameter']);
    exit;
}

// Return a more realistic column structure for testing
// This should be replaced with actual data from the database
$response = [
    'NumColumns' => 6,
    'NumDataColumns' => 3,
    'NumRows' => 1000,
    'TcolumnMask' => '1 1 1 1 1 1',
    'numVisibleColumns' => 6,
    'columnMask' => 63, // 2^6 - 1
    0 => [
        'Name' => 'HIV:INF/Genes/BOT:F/A/CD/D/R',
        'DataColumn' => 0,
        'StatName' => 'p-value',
        'GroupIndex' => 0,
        'IdColumn' => 0,
        'bg' => null
    ],
    1 => [
        'Name' => 'HIV:INF/Genes/BOT:F/A/CD/D/R',
        'DataColumn' => 1,
        'StatName' => 'p-value',
        'GroupIndex' => 0,
        'IdColumn' => 1,
        'bg' => null
    ],
    2 => [
        'Name' => 'HIV:INF/Genes/BOT:F/A/CD/D/R',
        'DataColumn' => 2,
        'StatName' => 'p-value',
        'GroupIndex' => 0,
        'IdColumn' => 2,
        'bg' => null
    ],
    3 => [
        'Name' => 'HIV:INF/Genes/BOT:F/A/CD/D/R',
        'DataColumn' => -1,
        'StatName' => '',
        'GroupIndex' => 1,
        'IdColumn' => 3,
        'bg' => null
    ],
    4 => [
        'Name' => 'HIV:INF/Genes/BOT:F/A/CD/D/R',
        'DataColumn' => -1,
        'StatName' => '',
        'GroupIndex' => 2,
        'IdColumn' => 4,
        'bg' => null
    ],
    5 => [
        'Name' => 'Genes',
        'DataColumn' => -1,
        'StatName' => '',
        'GroupIndex' => 3,
        'IdColumn' => 5,
        'bg' => null
    ]
];

header('Content-Type: application/json');
echo json_encode($response);
