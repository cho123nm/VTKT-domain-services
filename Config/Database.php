<?php
// Import Str helper class
use Illuminate\Support\Str;

// Trả về mảng cấu hình database
return [

    // Kết nối database mặc định (từ .env hoặc 'mysql')
    'default' => env('DB_CONNECTION', 'mysql'),

    // Các kết nối database có sẵn
    'connections' => [

        // Cấu hình MySQL
        'mysql' => [
            'driver' => 'mysql', // Driver database: mysql
            'url' => env('DATABASE_URL'), // URL kết nối database (nếu có)
            'host' => env('DB_HOST', '127.0.0.1'), // Host database (mặc định: localhost)
            'port' => env('DB_PORT', '3306'), // Port database (mặc định: 3306)
            'database' => env('DB_DATABASE', 'tenmien'), // Tên database (mặc định: tenmien)
            'username' => env('DB_USERNAME', 'root'), // Username database (mặc định: root)
            'password' => env('DB_PASSWORD', ''), // Password database (mặc định: rỗng)
            'unix_socket' => env('DB_SOCKET', ''), // Unix socket (nếu có)
            'charset' => 'utf8mb4', // Charset: utf8mb4 (hỗ trợ emoji và ký tự đặc biệt)
            'collation' => 'utf8mb4_unicode_ci', // Collation: utf8mb4_unicode_ci (so sánh không phân biệt hoa thường)
            'prefix' => '', // Prefix cho tên bảng (mặc định: không có prefix)
            'prefix_indexes' => true, // Thêm prefix vào index names
            'strict' => true, // Chế độ strict mode (bắt buộc kiểu dữ liệu chính xác)
            'engine' => null, // Database engine (null = mặc định của MySQL)
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                // Các tùy chọn PDO MySQL (chỉ thêm nếu extension pdo_mysql được load)
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'), // SSL CA certificate (nếu có)
            ]) : [], // Nếu không có pdo_mysql extension, dùng mảng rỗng
        ],

    ],

    // Thư mục chứa migration files
    'migrations' => 'migrations',

    // Cấu hình Redis (cache và session)
    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'), // Redis client (mặc định: phpredis)
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'), // Redis cluster mode (mặc định: redis)
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'), // Prefix cho Redis keys
        ],
        // Cấu hình Redis mặc định
        'default' => [
            'url' => env('REDIS_URL'), // Redis URL (nếu có)
            'host' => env('REDIS_HOST', '127.0.0.1'), // Redis host (mặc định: localhost)
            'username' => env('REDIS_USERNAME'), // Redis username (nếu có)
            'password' => env('REDIS_PASSWORD'), // Redis password (nếu có)
            'port' => env('REDIS_PORT', '6379'), // Redis port (mặc định: 6379)
            'database' => env('REDIS_DB', '0'), // Redis database number (mặc định: 0)
        ],
        // Cấu hình Redis cho cache
        'cache' => [
            'url' => env('REDIS_URL'), // Redis URL (nếu có)
            'host' => env('REDIS_HOST', '127.0.0.1'), // Redis host (mặc định: localhost)
            'username' => env('REDIS_USERNAME'), // Redis username (nếu có)
            'password' => env('REDIS_PASSWORD'), // Redis password (nếu có)
            'port' => env('REDIS_PORT', '6379'), // Redis port (mặc định: 6379)
            'database' => env('REDIS_CACHE_DB', '1'), // Redis database number cho cache (mặc định: 1)
        ],
    ],

];

