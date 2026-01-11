<?php
require 'vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    echo "SUCCESS: Loaded .env\n";
    echo "APP_KEY: " . ($_ENV['APP_KEY'] ?? 'NOT SET') . "\n";
    echo "APP_URL: " . ($_ENV['APP_URL'] ?? 'NOT SET') . "\n";
    echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'NOT SET') . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

