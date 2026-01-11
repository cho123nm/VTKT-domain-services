<?php
// Khai báo namespace cho Exception Handler này - thuộc App\Exceptions
namespace App\Exceptions;

// Import ExceptionHandler base class và Throwable interface
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler; // Base class xử lý exception của Laravel
use Throwable; // Interface cho tất cả các exception và error trong PHP

/**
 * Class Handler
 * Exception Handler chính của ứng dụng
 * Xử lý tất cả các exception và error trong ứng dụng
 */
class Handler extends ExceptionHandler
{
    /**
     * Danh sách các input không được flash vào session khi có validation exception
     * Các trường này chứa thông tin nhạy cảm (mật khẩu) nên không nên lưu vào session
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password', // Mật khẩu hiện tại
        'password', // Mật khẩu mới
        'password_confirmation', // Xác nhận mật khẩu mới
    ];

    /**
     * Đăng ký các callback xử lý exception cho ứng dụng
     * Có thể thêm logic để log hoặc xử lý các exception cụ thể ở đây
     * 
     * @return void
     */
    public function register(): void
    {
        // Đăng ký callback để report exception (ví dụ: gửi đến logging service)
        $this->reportable(function (Throwable $e) {
            // Hiện tại không có logic xử lý đặc biệt
            // Có thể thêm logic để gửi exception đến Sentry, Bugsnag, etc.
        });
    }
}

