<?php
// search.php - API endpoint for highway browser search functionality
// Routes to DisplayController::searchAction()

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

// Create a simple request object from GET parameters
$request = Request::createFromGlobals();

$module = $request->query->get('module');
$chr = $request->query->get('chr');
$search = $request->query->get('search');
$type = $request->query->get('type');

if (!$module || !$chr || !$search) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

// For now, return a simple search result for testing
// This should be replaced with actual search functionality
$response = [
    ['Test SNP 1', 100, $chr],
    ['Test SNP 2', 200, $chr],
    ['Test SNP 3', 300, $chr]
];

header('Content-Type: application/json');
echo json_encode($response);
