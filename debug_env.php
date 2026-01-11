<?php
// Script debug để kiểm tra các biến env có thể gây lỗi

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

echo "=== KIỂM TRA CÁC BIẾN ENV ===\n\n";

$envVars = [
    'APP_URL',
    'APP_KEY',
    'VIEW_COMPILED_PATH',
    'ASSET_URL',
    'MAIL_EHLO_DOMAIN',
    'SESSION_DOMAIN',
    'MYSQL_ATTR_SSL_CA',
    'FILESYSTEM_DISK',
];

foreach ($envVars as $var) {
    $value = env($var);
    echo "$var: " . ($value === null ? 'NULL' : ($value === '' ? 'EMPTY STRING' : $value)) . "\n";
}

echo "\n=== KIỂM TRA CÁC ĐƯỜNG DẪN ===\n\n";

$paths = [
    'base_path' => base_path(),
    'app_path' => app_path(),
    'resource_path' => resource_path(),
    'storage_path' => storage_path(),
    'public_path' => public_path(),
    'storage_path(framework/views)' => storage_path('framework/views'),
];

foreach ($paths as $name => $path) {
    echo "$name: $path\n";
    echo "  Exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
    echo "  Is dir: " . (is_dir($path) ? 'YES' : 'NO') . "\n";
}

echo "\n=== KIỂM TRA CONFIG VIEW ===\n\n";
try {
    $viewConfig = config('view');
    echo "compiled path: " . ($viewConfig['compiled'] ?? 'NOT SET') . "\n";
    echo "  Exists: " . (isset($viewConfig['compiled']) && file_exists($viewConfig['compiled']) ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== KIỂM TRA CONFIG FILESYSTEMS ===\n\n";
try {
    $fsConfig = config('filesystems');
    echo "default disk: " . ($fsConfig['default'] ?? 'NOT SET') . "\n";
    if (isset($fsConfig['disks']['public']['url'])) {
        echo "public url: " . $fsConfig['disks']['public']['url'] . "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

