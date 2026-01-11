<?php
/**
 * Script để patch LoadConfiguration.php để xử lý trường hợp config path rỗng
 */

$filePath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Foundation/Bootstrap/LoadConfiguration.php';

if (!file_exists($filePath)) {
    die("File not found: $filePath\n");
}

$content = file_get_contents($filePath);

// Tìm và thay thế method getConfigurationFiles
$oldMethod = <<<'PHP'
    protected function getConfigurationFiles(Application $app)
    {
        $files = [];

        $configPath = realpath($app->configPath());

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory.basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }
PHP;

$newMethod = <<<'PHP'
    protected function getConfigurationFiles(Application $app)
    {
        $files = [];

        $configPath = $app->configPath();
        
        // Kiểm tra và đảm bảo config path hợp lệ
        if (empty($configPath) || !is_dir($configPath)) {
            // Fallback về basePath/config
            $configPath = $app->basePath('config');
        }
        
        $realPath = realpath($configPath);
        
        // Nếu realpath trả về false, thử tạo thư mục hoặc dùng đường dẫn gốc
        if ($realPath === false) {
            // Thử tạo thư mục nếu chưa tồn tại
            if (!is_dir($configPath)) {
                @mkdir($configPath, 0755, true);
                $realPath = realpath($configPath);
            }
            
            // Nếu vẫn false, dùng đường dẫn gốc (không dùng realpath)
            if ($realPath === false) {
                $realPath = $configPath;
            }
        }
        
        // Kiểm tra lại trước khi dùng Finder
        if (empty($realPath) || !is_dir($realPath)) {
            throw new \RuntimeException("Config directory does not exist: {$configPath}");
        }

        foreach (Finder::create()->files()->name('*.php')->in($realPath) as $file) {
            $directory = $this->getNestedDirectory($file, $realPath);

            $files[$directory.basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }
PHP;

if (strpos($content, $oldMethod) !== false) {
    $newContent = str_replace($oldMethod, $newMethod, $content);
    file_put_contents($filePath, $newContent);
    echo "✅ Đã patch LoadConfiguration.php thành công!\n";
} else {
    echo "⚠️  Không tìm thấy method cần patch. Có thể đã được patch rồi hoặc code đã thay đổi.\n";
    echo "Đang kiểm tra xem có phải đã được patch chưa...\n";
    
    if (strpos($content, 'if (empty($configPath)') !== false) {
        echo "✅ File đã được patch rồi!\n";
    } else {
        echo "❌ Không thể patch tự động. Vui lòng kiểm tra thủ công.\n";
    }
}

