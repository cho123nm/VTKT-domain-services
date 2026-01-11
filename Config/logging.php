<?php
// Import các Monolog handler và processor
use Monolog\Handler\NullHandler; // Handler không làm gì (bỏ qua log)
use Monolog\Handler\StreamHandler; // Handler ghi log vào file
use Monolog\Handler\SyslogUdpHandler; // Handler ghi log vào syslog qua UDP
use Monolog\Processor\PsrLogMessageProcessor; // Processor format log message theo PSR-3

// Trả về mảng cấu hình logging
return [

    // Log channel mặc định (từ .env hoặc 'stack')
    'default' => env('LOG_CHANNEL', 'stack'),

    // Cấu hình logging cho deprecation warnings
    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'), // Channel để log deprecations (mặc định: null - không log)
        'trace' => false, // Có log stack trace không (false = không)
    ],

    // Các log channels có sẵn
    'channels' => [
        // Stack channel - kết hợp nhiều channels lại với nhau
        'stack' => [
            'driver' => 'stack', // Driver: stack (kết hợp nhiều channels)
            'channels' => ['single'], // Các channels được kết hợp: chỉ có 'single'
            'ignore_exceptions' => false, // Có bỏ qua exceptions không (false = không bỏ qua)
        ],

        // Single channel - ghi log vào một file duy nhất
        'single' => [
            'driver' => 'single', // Driver: single (một file)
            'path' => storage_path('logs/laravel.log'), // Đường dẫn file log: storage/logs/laravel.log
            'level' => env('LOG_LEVEL', 'debug'), // Log level: debug, info, warning, error, critical (mặc định: debug)
            'replace_placeholders' => true, // Có thay thế placeholders trong log message không (true = có)
        ],

        // Null channel - không log gì cả (dùng cho testing hoặc disable logging)
        'null' => [
            'driver' => 'monolog', // Driver: monolog
            'handler' => NullHandler::class, // Handler: NullHandler (không làm gì)
        ],
    ],

];

