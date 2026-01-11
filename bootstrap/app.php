<?php
/*
|--------------------------------------------------------------------------
| Tạo Application Instance
|--------------------------------------------------------------------------
|
| Điều đầu tiên chúng ta làm là tạo một instance Laravel application mới
| Instance này đóng vai trò là "chất kết dính" cho tất cả các component của Laravel,
| và là IoC container cho hệ thống, binding tất cả các phần khác nhau lại với nhau.
|
*/

// Tạo Laravel application instance
// APP_BASE_PATH từ .env hoặc mặc định là thư mục cha của bootstrap
$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__) // Đường dẫn base của ứng dụng
);

/*
|--------------------------------------------------------------------------
| Bind Các Interface Quan Trọng
|--------------------------------------------------------------------------
|
| Tiếp theo, chúng ta cần bind một số interface quan trọng vào container
| để có thể resolve chúng khi cần. Các kernel phục vụ các request đến
| ứng dụng này từ cả web và CLI (command line).
|
*/

// Bind HTTP Kernel - xử lý các HTTP request từ web
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class, // Interface HTTP Kernel
    App\Http\Kernel::class // Implementation của HTTP Kernel
);

// Bind Console Kernel - xử lý các command từ CLI (Artisan)
$app->singleton(
    Illuminate\Contracts\Console\Kernel::class, // Interface Console Kernel
    App\Console\Kernel::class // Implementation của Console Kernel
);

// Bind Exception Handler - xử lý các exception và error
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class, // Interface Exception Handler
    App\Exceptions\Handler::class // Implementation của Exception Handler
);

/*
|--------------------------------------------------------------------------
| Trả Về Application Instance
|--------------------------------------------------------------------------
|
| Script này trả về application instance. Instance được cung cấp cho
| script gọi để chúng ta có thể tách việc xây dựng instance khỏi việc
| chạy ứng dụng thực tế và gửi response.
|
*/

// Trả về application instance
return $app;

