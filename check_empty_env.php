<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== KIỂM TRA CÁC BIẾN ENV RỖNG ===\n\n";

$envFile = file_get_contents('.env');
$lines = explode("\n", $envFile);

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '#') === 0) {
        continue;
    }
    
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Kiểm tra các biến có thể là đường dẫn
        if (empty($value) && (
            stripos($key, 'PATH') !== false ||
            stripos($key, 'URL') !== false ||
            stripos($key, 'DOMAIN') !== false ||
            stripos($key, 'DIR') !== false
        )) {
            echo "WARNING: $key is EMPTY (could cause directory error)\n";
        }
    }
}

echo "\n=== KIỂM TRA CONFIG VIEW ===\n\n";
try {
    $app = require_once 'bootstrap/app.php';
    $viewConfig = $app->make('config')->get('view');
    echo "View paths: " . print_r($viewConfig['paths'] ?? [], true) . "\n";
    echo "Compiled path: " . ($viewConfig['compiled'] ?? 'NOT SET') . "\n";
    
    if (isset($viewConfig['compiled']) && empty($viewConfig['compiled'])) {
        echo "ERROR: Compiled path is EMPTY!\n";
    }
} catch (Exception $e) {
    echo "ERROR loading config: " . $e->getMessage() . "\n";
}

