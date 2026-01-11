<?php
// Import Str helper class
use Illuminate\Support\Str;

// Trả về mảng cấu hình session
return [

    'driver' => env('SESSION_DRIVER', 'file'), // Driver lưu trữ session (file, database, redis, etc.) - mặc định: file

    'lifetime' => env('SESSION_LIFETIME', 120), // Thời gian sống của session (phút) - mặc định: 120 phút (2 giờ)

    'expire_on_close' => false, // Session hết hạn khi đóng browser không (false = không, true = có)

    'encrypt' => false, // Có mã hóa session data không (false = không mã hóa)

    'files' => storage_path('framework/sessions'), // Đường dẫn lưu file session (nếu driver = 'file')

    'connection' => env('SESSION_CONNECTION'), // Database connection name (nếu driver = 'database')

    'table' => 'sessions', // Tên bảng lưu session (nếu driver = 'database')

    'store' => env('SESSION_STORE'), // Cache store name (nếu driver = 'cache')

    'lottery' => [2, 100], // Xác suất garbage collection: 2/100 = 2% mỗi request (để cleanup session cũ)

    'cookie' => env(
        'SESSION_COOKIE', // Tên cookie session từ .env (nếu có)
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session' // Mặc định: tên app + '_session' (ví dụ: cloudstorevn_session)
    ),

    'path' => '/', // Path của cookie (mặc định: / - toàn bộ domain)

    'domain' => env('SESSION_DOMAIN') ?: null, // Domain của cookie (null = domain hiện tại)

    'secure' => env('SESSION_SECURE_COOKIE'), // Cookie chỉ gửi qua HTTPS không (true/false/null)

    'http_only' => true, // Cookie chỉ accessible qua HTTP không (true = không thể truy cập qua JavaScript)

    'same_site' => 'lax', // SameSite attribute: 'lax', 'strict', hoặc 'none' (bảo vệ CSRF)

];

