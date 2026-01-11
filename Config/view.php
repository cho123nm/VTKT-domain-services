<?php
// Trả về mảng cấu hình view
return [

    /*
    |--------------------------------------------------------------------------
    | Đường Dẫn Lưu Trữ Views
    |--------------------------------------------------------------------------
    | Các thư mục chứa Blade templates của ứng dụng
    */

    'paths' => [
        resource_path('views'), // Đường dẫn đến thư mục views (resources/views)
    ],

    /*
    |--------------------------------------------------------------------------
    | Đường Dẫn Compiled Views
    |--------------------------------------------------------------------------
    | Thư mục lưu các view đã được compile (Blade -> PHP)
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH', // Đường dẫn compiled views từ .env (nếu có)
        realpath(storage_path('framework/views')) // Mặc định: storage/framework/views (realpath để lấy đường dẫn tuyệt đối)
    ),

];

