<?php
// Khai báo namespace cho Middleware này - thuộc App\Http\Middleware
namespace App\Http\Middleware;

// Import Middleware base class và Log facade
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware; // Base class cho CSRF verification middleware
use Illuminate\Support\Facades\Log; // Facade để ghi log

/**
 * Class VerifyCsrfToken
 * Middleware xác thực CSRF token
 * Bảo vệ ứng dụng khỏi Cross-Site Request Forgery attacks
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * Danh sách các URI không cần xác thực CSRF token
     * Các route này được exclude khỏi CSRF verification (ví dụ: webhook, callback)
     *
     * @var array<int, string>
     */
    protected $except = [
        '/callback', // Callback từ cổng nạp thẻ (không có CSRF token)
        '/telegram/webhook', // Telegram webhook (không có CSRF token)
        'admin/logout', // Admin logout (để tránh lỗi CSRF khi logout)
        // Tạm thời bỏ qua CSRF để debug - sẽ xóa sau
        // 'checkout/domain/process', // Đã comment - không cần thiết nữa
    ];
    
    /**
     * Xử lý incoming request
     * Ghi log cho các checkout routes và xử lý CSRF verification
     *
     * @param  \Illuminate\Http\Request  $request - HTTP request hiện tại
     * @param  \Closure  $next - Closure để chuyển request đến middleware tiếp theo
     * @return mixed - Response từ middleware tiếp theo hoặc exception
     */
    public function handle($request, \Closure $next)
    {
        // Ghi log CSRF verification cho các checkout routes (để debug)
        if (str_contains($request->path(), 'checkout') || str_contains($request->path(), 'domain/process')) {
            Log::info('CSRF Check - Checkout route', [
                'path' => $request->path(), // Đường dẫn request
                'method' => $request->method(), // Phương thức HTTP (GET, POST, etc.)
                'has_token' => $request->has('_token'), // Có token trong request không
                'token' => $request->input('_token') ? 'present' : 'missing', // Token có trong input không
                'token_header' => $request->header('X-CSRF-TOKEN') ? 'present' : 'missing', // Token có trong header không
                'session_id' => session()->getId(), // ID session
                'has_users_session' => session()->has('users'), // Có session 'users' không
                'users_value' => session('users'), // Giá trị session 'users'
                'cookie_header' => $request->header('Cookie') ? 'present' : 'missing' // Có cookie header không
            ]);
        }
        
        try {
            // Gọi phương thức handle của parent class để xác thực CSRF token
            return parent::handle($request, $next);
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            // Nếu CSRF token không khớp, ghi log lỗi và throw exception
            Log::error('CSRF Token Mismatch', [
                'path' => $request->path(), // Đường dẫn request
                'method' => $request->method(), // Phương thức HTTP
                'session_id' => session()->getId(), // ID session
                'has_users_session' => session()->has('users'), // Có session 'users' không
                'token_in_request' => $request->input('_token'), // Token trong request
                'token_in_header' => $request->header('X-CSRF-TOKEN'), // Token trong header
                'session_token' => session()->token() // Token trong session
            ]);
            // Throw exception để Laravel xử lý (sẽ trả về 419 error)
            throw $e;
        }
    }
}

