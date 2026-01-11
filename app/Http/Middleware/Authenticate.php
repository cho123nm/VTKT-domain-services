<?php
// Khai báo namespace cho Middleware này - thuộc App\Http\Middleware
namespace App\Http\Middleware;

// Import Middleware base class và Request class
use Illuminate\Auth\Middleware\Authenticate as Middleware; // Base class cho authentication middleware
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class Authenticate
 * Middleware xác thực user
 * Kiểm tra user đã đăng nhập chưa, nếu chưa thì redirect đến trang đăng nhập
 */
class Authenticate extends Middleware
{
    /**
     * Lấy đường dẫn mà user sẽ được redirect đến khi chưa được xác thực
     * 
     * @param Request $request - HTTP request hiện tại
     * @return string|null - Đường dẫn redirect hoặc null nếu request mong đợi JSON response
     */
    protected function redirectTo(Request $request): ?string
    {
        // Nếu request mong đợi JSON response (AJAX), trả về null (sẽ trả về JSON error)
        // Nếu không, redirect đến trang đăng nhập
        return $request->expectsJson() ? null : route('login');
    }
}

