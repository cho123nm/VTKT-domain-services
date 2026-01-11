<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $app = require_once 'bootstrap/app.php';
    $config = $app->make('config');
    
    echo "=== VIEW CONFIG ===\n";
    $viewPaths = $config->get('view.paths', []);
    $compiled = $config->get('view.compiled', '');
    
    echo "View paths:\n";
    foreach ($viewPaths as $path) {
        echo "  - $path\n";
        if (empty($path)) {
            echo "    ERROR: Path is EMPTY!\n";
        }
    }
    
    echo "\nCompiled path: $compiled\n";
    if (empty($compiled)) {
        echo "ERROR: Compiled path is EMPTY!\n";
    } else {
        echo "Exists: " . (file_exists($compiled) ? 'YES' : 'NO') . "\n";
        echo "Is dir: " . (is_dir($compiled) ? 'YES' : 'NO') . "\n";
    }
    
    echo "\n=== FILESYSTEM CONFIG ===\n";
    $fsConfig = $config->get('filesystems.disks.public', []);
    if (isset($fsConfig['url'])) {
        echo "Public URL: " . $fsConfig['url'] . "\n";
        if (empty($fsConfig['url'])) {
            echo "ERROR: Public URL is EMPTY!\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

