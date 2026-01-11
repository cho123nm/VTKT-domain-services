<?php
// Khai báo namespace cho HTTP Kernel này - thuộc App\Http
namespace App\Http;

// Import HTTP Kernel base class
use Illuminate\Foundation\Http\Kernel as HttpKernel; // Base class cho HTTP Kernel

/**
 * Class Kernel
 * HTTP Kernel của ứng dụng
 * Quản lý middleware stack và xử lý các HTTP request
 */
class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack của ứng dụng
     * Các middleware này được chạy trong mọi request đến ứng dụng
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class, // Trust hosts middleware (đã comment)
        \App\Http\Middleware\TrustProxies::class, // Trust proxies middleware (cho load balancer, reverse proxy)
        \Illuminate\Http\Middleware\HandleCors::class, // CORS middleware (Cross-Origin Resource Sharing)
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class, // Ngăn request khi đang bảo trì
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class, // Validate kích thước POST request
        \App\Http\Middleware\TrimStrings::class, // Trim strings trong request
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class, // Chuyển empty string thành null
    ];

    /**
     * Các nhóm middleware của ứng dụng
     * Các middleware được nhóm lại để dễ quản lý và áp dụng cho routes
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        // Nhóm middleware 'web' - cho các web routes
        'web' => [
            \App\Http\Middleware\EncryptCookies::class, // Mã hóa cookies
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class, // Thêm cookies đã queue vào response
            \Illuminate\Session\Middleware\StartSession::class, // Bắt đầu session
            \Illuminate\View\Middleware\ShareErrorsFromSession::class, // Chia sẻ errors từ session với views
            \App\Http\Middleware\VerifyCsrfToken::class, // Verify CSRF token
            \Illuminate\Routing\Middleware\SubstituteBindings::class, // Thay thế route model bindings
        ],

        // Nhóm middleware 'api' - cho các API routes
        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Sanctum middleware (đã comment)
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api', // Rate limiting cho API (sử dụng rate limiter 'api')
            \Illuminate\Routing\Middleware\SubstituteBindings::class, // Thay thế route model bindings
        ],
    ];

    /**
     * Middleware aliases của ứng dụng
     * Aliases có thể được dùng thay cho class name để gán middleware cho routes và groups
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class, // Middleware xác thực user
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class, // Basic authentication
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class, // Xác thực session
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class, // Thiết lập cache headers
        'can' => \Illuminate\Auth\Middleware\Authorize::class, // Kiểm tra quyền (authorization)
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class, // Redirect nếu đã đăng nhập (cho trang login/register)
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class, // Yêu cầu xác nhận mật khẩu
        'signed' => \App\Http\Middleware\ValidateSignature::class, // Validate signed URL
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class, // Rate limiting
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // Đảm bảo email đã được verify
    ];
}

