<?php
// Khai báo namespace cho ServiceProvider này - thuộc App\Providers
namespace App\Providers;

// Import các class cần thiết
use Illuminate\Cache\RateLimiting\Limit; // Class để giới hạn rate limit
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider; // Base class cho route service provider
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\RateLimiter; // Facade để quản lý rate limiting
use Illuminate\Support\Facades\Route; // Facade để định nghĩa routes

/**
 * Class RouteServiceProvider
 * Service Provider quản lý routes của ứng dụng
 * Định nghĩa rate limiting và load các file routes
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Đường dẫn đến route "home" của ứng dụng
     * Thường được dùng để redirect user sau khi đăng nhập thành công
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Định nghĩa route model bindings, pattern filters và các cấu hình route khác
     * Phương thức này được gọi khi ứng dụng khởi động
     * 
     * @return void
     */
    public function boot(): void
    {
        // Định nghĩa rate limiter cho API routes
        // Giới hạn số lượng request từ mỗi user/IP
        RateLimiter::for('api', function (Request $request) {
            // Sử dụng session user thay vì auth guard để tránh lỗi "Auth guard [] is not defined"
            $userId = null; // ID user (mặc định: null)
            // Kiểm tra user đã đăng nhập chưa (có session 'users')
            if (session()->has('users')) {
                // Tìm user trong database theo username trong session
                $user = \App\Models\User::findByUsername(session('users'));
                $userId = $user ? $user->id : null; // Lấy ID user nếu tìm thấy
            }
            // Giới hạn 60 requests/phút, phân biệt theo user ID hoặc IP address
            return Limit::perMinute(60)->by($userId ?: $request->ip());
        });

        // Đăng ký các file routes
        $this->routes(function () {
            // Đăng ký API routes (từ routes/api.php)
            Route::middleware('api') // Áp dụng 'api' middleware
                ->prefix('api') // Thêm prefix 'api' vào URL
                ->group(base_path('routes/api.php')); // Load file routes/api.php

            // Đăng ký Web routes (từ routes/web.php)
            Route::middleware('web') // Áp dụng 'web' middleware (session, CSRF, etc.)
                ->group(base_path('routes/web.php')); // Load file routes/web.php
        });
    }
}

