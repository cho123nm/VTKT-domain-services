<?php
// Import Facade và ServiceProvider classes
use Illuminate\Support\Facades\Facade; // Facade base class
use Illuminate\Support\ServiceProvider; // ServiceProvider base class

// Trả về mảng cấu hình ứng dụng
return [

    /*
    |--------------------------------------------------------------------------
    | Tên Ứng Dụng
    |--------------------------------------------------------------------------
    | Tên của ứng dụng, được dùng trong các thông báo và email
    */

    'name' => env('APP_NAME', 'CloudStoreVN'), // Tên ứng dụng (từ .env hoặc mặc định: CloudStoreVN)

    /*
    |--------------------------------------------------------------------------
    | Môi Trường Ứng Dụng
    |--------------------------------------------------------------------------
    | Xác định môi trường chạy ứng dụng: local, staging, production, etc.
    */

    'env' => env('APP_ENV', 'production'), // Môi trường ứng dụng (từ .env hoặc mặc định: production)

    /*
    |--------------------------------------------------------------------------
    | Chế Độ Debug
    |--------------------------------------------------------------------------
    | Bật/tắt chế độ debug. Khi bật, sẽ hiển thị chi tiết lỗi.
    */

    'debug' => (bool) env('APP_DEBUG', false), // Chế độ debug (từ .env hoặc mặc định: false) - ép kiểu về boolean

    /*
    |--------------------------------------------------------------------------
    | URL Ứng Dụng
    |--------------------------------------------------------------------------
    | URL gốc của ứng dụng, được dùng để tạo các link tuyệt đối
    */

    'url' => env('APP_URL', 'http://localhost'), // URL ứng dụng (từ .env hoặc mặc định: http://localhost)

    'asset_url' => env('ASSET_URL') ?: null, // URL cho assets (CSS, JS, images) nếu khác với APP_URL

    /*
    |--------------------------------------------------------------------------
    | Múi Giờ Ứng Dụng
    |--------------------------------------------------------------------------
    | Múi giờ mặc định cho ứng dụng. Tất cả datetime sẽ được convert sang múi giờ này.
    */

    'timezone' => 'Asia/Ho_Chi_Minh', // Múi giờ: Asia/Ho_Chi_Minh (GMT+7)

    /*
    |--------------------------------------------------------------------------
    | Cấu Hình Ngôn Ngữ Ứng Dụng
    |--------------------------------------------------------------------------
    | Ngôn ngữ mặc định và fallback locale cho ứng dụng
    */

    'locale' => 'vi', // Ngôn ngữ mặc định: tiếng Việt

    'fallback_locale' => 'en', // Ngôn ngữ dự phòng: tiếng Anh (nếu không tìm thấy translation tiếng Việt)

    'faker_locale' => 'en_US', // Locale cho Faker (fake data generator): tiếng Anh Mỹ

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    | Key được dùng để mã hóa dữ liệu trong ứng dụng (session, cookies, etc.)
    */

    'key' => env('APP_KEY'), // Encryption key từ .env (phải được set khi cài đặt)

    'cipher' => 'AES-256-CBC', // Thuật toán mã hóa: AES-256-CBC

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    | Driver được dùng để lưu trữ trạng thái maintenance mode
    */

    'maintenance' => [
        'driver' => 'file', // Driver: file (lưu vào file storage/framework/down)
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Providers Tự Động Load
    |--------------------------------------------------------------------------
    | Danh sách các service providers được tự động load khi ứng dụng khởi động
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        // Merge các service providers mặc định của Laravel với các service providers tùy chỉnh
        App\Providers\AppServiceProvider::class, // Service provider chính của ứng dụng
        App\Providers\RouteServiceProvider::class, // Service provider quản lý routes
    ])->toArray(), // Chuyển thành mảng

    /*
    |--------------------------------------------------------------------------
    | Class Aliases (Facades)
    |--------------------------------------------------------------------------
    | Danh sách các class aliases (facades) có thể được dùng trong ứng dụng
    */

    'aliases' => Facade::defaultAliases()->merge([
        // Merge các aliases mặc định của Laravel với các aliases tùy chỉnh
        // 'Example' => App\Facades\Example::class, // Ví dụ alias (đã comment)
    ])->toArray(), // Chuyển thành mảng

];

