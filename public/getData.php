<?php
// getData.php - API endpoint for highway browser row data
// Routes to DisplayController::dataAction()

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

// Create a simple request object from GET parameters
$request = Request::createFromGlobals();

$module = $request->query->get('module');
$chr = $request->query->get('chr');
$rowStart = $request->query->get('rowStart');
$rowCount = $request->query->get('rowCount');
$threshold = $request->query->get('threshold');

if (!$module || !$chr || $rowStart === null || $rowCount === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters: module=' . $module . ', chr=' . $chr . ', rowStart=' . $rowStart . ', rowCount=' . $rowCount]);
    exit;
}

// For now, return mock data for testing
// This should be replaced with actual data from the database
$rows = [];
for ($i = 0; $i < $rowCount; $i++) {
    $rowIndex = $rowStart + $i;
    $rows[] = [
        'Name' => 'SNP_' . $rowIndex,
        'Coords' => $rowIndex * 1000,
        'Gene' => 'GENE_' . ($rowIndex % 10),
        'nrow' => $rowIndex,
        'Data' => [
            rand(0, 100) / 100, // Mock p-value
            rand(0, 100) / 100, // Mock p-value
            rand(0, 100) / 100  // Mock p-value
        ]
    ];
}

$response = [
    'Rows' => $rows
];

header('Content-Type: application/json');
echo json_encode($response);
