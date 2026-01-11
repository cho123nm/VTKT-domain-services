<?php
// Trả về mảng cấu hình filesystem
return [

    // Filesystem disk mặc định (từ .env hoặc 'local')
    'default' => env('FILESYSTEM_DISK', 'local'),

    // Các filesystem disks có sẵn
    'disks' => [

        // Local disk - lưu trữ file trên local filesystem
        'local' => [
            'driver' => 'local', // Driver: local (lưu trên server)
            'root' => storage_path('app'), // Thư mục gốc: storage/app
            'throw' => false, // Có throw exception khi lỗi không (false = không throw)
        ],

        // Public disk - lưu trữ file công khai (có thể truy cập qua URL)
        'public' => [
            'driver' => 'local', // Driver: local (lưu trên server)
            'root' => storage_path('app/public'), // Thư mục gốc: storage/app/public
            'url' => env('APP_URL').'/storage', // URL để truy cập file: APP_URL/storage
            'visibility' => 'public', // Visibility: public (công khai)
            'throw' => false, // Có throw exception khi lỗi không (false = không throw)
        ],

    ],

    // Symbolic links - tạo symlink từ public/storage đến storage/app/public
    'links' => [
        public_path('storage') => storage_path('app/public'), // public/storage -> storage/app/public
    ],

];

