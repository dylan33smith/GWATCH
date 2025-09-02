<?php
// report.php - API endpoint for highway browser report generation
// Routes to DisplayController::reportAction()

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Create a simple request object from GET parameters
$request = Request::createFromGlobals();

$module = $request->query->get('module');
$chr = $request->query->get('chr');
$row = $request->query->get('row');
$format = $request->query->get('format');
$reportType = $request->query->get('reportType');

if (!$module || !$chr || !$row || !$format || !$reportType) {
    http_response_code(400);
    echo 'Missing required parameters';
    exit;
}

// For now, return a simple SVG for testing
// This should be replaced with actual report generation
$svgContent = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="400" height="300" xmlns="http://www.w3.org/2000/svg">
  <rect width="400" height="300" fill="white" stroke="black" stroke-width="2"/>
  <text x="200" y="150" text-anchor="middle" font-family="Arial" font-size="16">
    SONGBIRD Report - Module: ' . htmlspecialchars($module) . ', Chr: ' . htmlspecialchars($chr) . ', Row: ' . htmlspecialchars($row) . '
  </text>
  <text x="200" y="180" text-anchor="middle" font-family="Arial" font-size="12">
    Type: ' . htmlspecialchars($reportType) . ', Format: ' . htmlspecialchars($format) . '
  </text>
</svg>';

if ($format === 'svg') {
    header('Content-Type: image/svg+xml');
    echo $svgContent;
} else if ($format === 'pdf') {
    // PDF generation would go here
    header('Content-Type: text/plain');
    echo 'PDF generation not yet implemented';
} else {
    http_response_code(400);
    echo 'Unsupported format';
}
