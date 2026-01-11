<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Test các helper functions
echo "=== TEST HELPER FUNCTIONS ===\n";
echo "base_path(): " . base_path() . "\n";
echo "storage_path(): " . storage_path() . "\n";
echo "storage_path('framework/views'): " . storage_path('framework/views') . "\n";
echo "resource_path('views'): " . resource_path('views') . "\n";

// Test load config trực tiếp
echo "\n=== TEST LOAD CONFIG DIRECTLY ===\n";
$viewConfig = require 'config/view.php';
echo "Compiled path from config: " . ($viewConfig['compiled'] ?? 'NOT SET') . "\n";

if (isset($viewConfig['compiled'])) {
    $compiled = $viewConfig['compiled'];
    if (empty($compiled)) {
        echo "ERROR: Compiled path is EMPTY STRING!\n";
    } else {
        echo "Compiled path exists: " . (file_exists($compiled) ? 'YES' : 'NO') . "\n";
        echo "Compiled path is dir: " . (is_dir($compiled) ? 'YES' : 'NO') . "\n";
    }
}

// Test env variables
echo "\n=== TEST ENV VARIABLES ===\n";
echo "VIEW_COMPILED_PATH: " . (env('VIEW_COMPILED_PATH') ?: 'NOT SET (will use default)') . "\n";
echo "APP_URL: " . (env('APP_URL') ?: 'NOT SET') . "\n";

