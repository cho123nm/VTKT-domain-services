<?php
// Import các class cần thiết
use Illuminate\Contracts\Http\Kernel; // Interface HTTP Kernel
use Illuminate\Http\Request; // Class xử lý HTTP request

// Định nghĩa hằng số LARAVEL_START để đo thời gian khởi động ứng dụng
define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Kiểm Tra Ứng Dụng Có Đang Bảo Trì Không
|--------------------------------------------------------------------------
|
| Nếu ứng dụng đang ở chế độ bảo trì / demo mode thông qua lệnh "down"
| chúng ta sẽ load file này để hiển thị nội dung đã được render sẵn
| thay vì khởi động framework, điều này có thể gây ra exception.
|
*/

// Kiểm tra file maintenance.php có tồn tại không
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance; // Load file maintenance để hiển thị trang bảo trì
}

/*
|--------------------------------------------------------------------------
| Đăng Ký Auto Loader
|--------------------------------------------------------------------------
|
| Composer cung cấp một class loader tiện lợi, tự động được tạo cho
| ứng dụng này. Chúng ta chỉ cần sử dụng nó! Chúng ta sẽ require nó
| vào script ở đây để không cần phải load các class thủ công.
|
*/

// Load Composer autoloader để tự động load các class
require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Chạy Ứng Dụng
|--------------------------------------------------------------------------
|
| Một khi chúng ta đã có application instance, chúng ta có thể xử lý
| request đến bằng cách sử dụng HTTP kernel của ứng dụng. Sau đó,
| chúng ta sẽ gửi response trở lại trình duyệt của client, cho phép
| họ sử dụng ứng dụng của chúng ta.
|
*/

// Load application instance từ bootstrap/app.php
$app = require_once __DIR__.'/../bootstrap/app.php';

// Tạo HTTP Kernel instance từ application container
$kernel = $app->make(Kernel::class);

// Xử lý request và gửi response
// Request::capture() tự động capture request từ PHP superglobals ($_GET, $_POST, etc.)
$response = $kernel->handle(
    $request = Request::capture() // Capture request hiện tại
)->send(); // Gửi response về client

// Gọi phương thức terminate để cleanup sau khi gửi response
$kernel->terminate($request, $response);

