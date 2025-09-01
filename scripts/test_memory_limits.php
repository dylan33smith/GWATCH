<?php

/**
 * Memory Limit Testing Script
 * 
 * This script helps you understand the memory requirements for different dataset sizes
 * and test the limits of your system.
 */

echo "=== GWatch Memory Limit Testing ===\n\n";

// Get system information
$totalMemory = shell_exec('free -m | grep Mem | awk \'{print $2}\'');
$availableMemory = shell_exec('free -m | grep Mem | awk \'{print $7}\'');
$phpMemoryLimit = ini_get('memory_limit');

echo "System Information:\n";
echo "- Total RAM: " . trim($totalMemory) . "MB\n";
echo "- Available RAM: " . trim($availableMemory) . "MB\n";
echo "- PHP Memory Limit: {$phpMemoryLimit}\n\n";

// Calculate safe limits
$totalMemoryMB = (int) trim($totalMemory);
$availableMemoryMB = (int) trim($availableMemory);
$safeMemoryLimit = min($availableMemoryMB * 0.8, $totalMemoryMB * 0.7);

echo "Recommended Limits:\n";
echo "- Safe memory limit: " . round($safeMemoryLimit) . "MB (" . round($safeMemoryLimit / 1024, 2) . "GB)\n";
echo "- Maximum CSV file size: " . round($safeMemoryLimit / 3) . "MB (estimated)\n";
echo "- Maximum total dataset size: " . round($safeMemoryLimit / 2) . "MB (conservative)\n\n";

// Test with different memory limits
echo "Testing different memory limits:\n";
$testLimits = ['512M', '1G', '2G', '4G', '8G'];

foreach ($testLimits as $limit) {
    $limitBytes = parseMemoryLimit($limit);
    $limitMB = round($limitBytes / 1024 / 1024);
    
    if ($limitMB <= $safeMemoryLimit) {
        echo "✓ {$limit} ({$limitMB}MB) - SAFE\n";
    } else {
        echo "⚠ {$limit} ({$limitMB}MB) - MAY CAUSE OOM\n";
    }
}

echo "\n=== Usage Examples ===\n";
echo "For small datasets (< 100MB):\n";
echo "  php bin/console app:create-module data.zip \"Module\" \"Description\" --memory-limit=1G\n\n";

echo "For medium datasets (100MB - 500MB):\n";
echo "  php bin/console app:create-module data.zip \"Module\" \"Description\" --memory-limit=2G\n\n";

echo "For large datasets (500MB - 1GB):\n";
echo "  php bin/console app:create-module data.zip \"Module\" \"Description\" --memory-limit=4G\n\n";

echo "For very large datasets (> 1GB):\n";
echo "  php bin/console app:create-module data.zip \"Module\" \"Description\" --memory-limit=8G --force\n\n";

echo "=== Troubleshooting ===\n";
echo "If you get 'Killed' messages:\n";
echo "1. Reduce the --memory-limit value\n";
echo "2. Split large datasets into smaller ZIP files\n";
echo "3. Increase system RAM or add swap space\n";
echo "4. Use --force to bypass memory warnings (risky)\n\n";

function parseMemoryLimit(string $memoryLimit): int
{
    $memoryLimit = strtolower(trim($memoryLimit));
    
    if ($memoryLimit === '-1') {
        return PHP_INT_MAX;
    }
    
    $unit = substr($memoryLimit, -1);
    $value = (int) substr($memoryLimit, 0, -1);
    
    switch ($unit) {
        case 'g':
            return $value * 1024 * 1024 * 1024;
        case 'm':
            return $value * 1024 * 1024;
        case 'k':
            return $value * 1024;
        default:
            return (int) $memoryLimit;
    }
}

