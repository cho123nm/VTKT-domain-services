<?php
/**
 * Script để tìm đường dẫn rỗng gây ra DirectoryNotFoundException
 */

$basePath = __DIR__;

// Kiểm tra xem có phải đang chạy trong container không
if (!file_exists($basePath . '/config/view.php')) {
    // Có thể đang chạy trong container với đường dẫn khác
    $basePath = '/var/www/html';
    if (!file_exists($basePath . '/config/view.php')) {
        die("Cannot find config/view.php. Base path: " . __DIR__ . "\n");
    }
}

require $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';

echo "=== CHECKING PATHS ===\n";
echo "Base path: $basePath\n\n";

// Check view paths
try {
    $viewConfig = require $basePath . '/config/view.php';
    echo "View paths:\n";
    foreach ($viewConfig['paths'] as $i => $path) {
        $resolved = is_callable($path) ? $path() : $path;
        echo "  [$i] Original: " . (is_string($path) ? $path : 'CALLABLE') . "\n";
        echo "      Resolved: " . $resolved . "\n";
        echo "      Empty: " . (empty($resolved) ? 'YES - PROBLEM!' : 'NO') . "\n";
        echo "      Exists: " . (file_exists($resolved) ? 'YES' : 'NO') . "\n\n";
    }
    
    $compiled = $viewConfig['compiled'];
    $compiledResolved = is_callable($compiled) ? $compiled() : $compiled;
    echo "Compiled path:\n";
    echo "  Original: " . (is_string($compiled) ? $compiled : 'CALLABLE') . "\n";
    echo "  Resolved: " . $compiledResolved . "\n";
    echo "  Empty: " . (empty($compiledResolved) ? 'YES - PROBLEM!' : 'NO') . "\n";
    echo "  Exists: " . (file_exists($compiledResolved) ? 'YES' : 'NO') . "\n\n";
} catch (\Exception $e) {
    echo "Error checking view config: " . $e->getMessage() . "\n\n";
}

// Check all config files that might have paths
$configFiles = [
    'app.php',
    'cache.php',
    'database.php',
    'filesystems.php',
    'logging.php',
    'mail.php',
    'queue.php',
    'session.php',
];

foreach ($configFiles as $file) {
    $filePath = $basePath . '/config/' . $file;
    if (!file_exists($filePath)) {
        continue;
    }
    
    try {
        $config = require $filePath;
        echo "=== Checking $file ===\n";
        checkArrayForEmptyPaths($config, $file);
        echo "\n";
    } catch (\Exception $e) {
        echo "Error checking $file: " . $e->getMessage() . "\n\n";
    }
}

function checkArrayForEmptyPaths($array, $prefix = '') {
    foreach ($array as $key => $value) {
        $fullKey = $prefix ? "$prefix.$key" : $key;
        
        if (is_string($value) && (empty($value) || $value === '')) {
            echo "  FOUND EMPTY PATH: $fullKey = '$value'\n";
        } elseif (is_array($value)) {
            checkArrayForEmptyPaths($value, $fullKey);
        } elseif (is_callable($value)) {
            try {
                $resolved = $value();
                if (is_string($resolved) && empty($resolved)) {
                    echo "  FOUND EMPTY RESOLVED PATH: $fullKey = '$resolved'\n";
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }
    }
}

