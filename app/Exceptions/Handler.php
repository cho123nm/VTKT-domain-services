<?php
// Khai báo namespace cho Exception Handler này - thuộc App\Exceptions
namespace App\Exceptions;

// Import ExceptionHandler base class và Throwable interface
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler; // Base class xử lý exception của Laravel
use Throwable; // Interface cho tất cả các exception và error trong PHP
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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

    /**
     * Đăng ký error view paths
     * Override để xử lý trường hợp đường dẫn rỗng
     * 
     * @return void
     */
    protected function registerErrorViewPaths(): void
    {
        try {
            parent::registerErrorViewPaths();
        } catch (\Exception $e) {
            // Bỏ qua lỗi nếu có đường dẫn rỗng
            // Error views sẽ không được đăng ký nhưng app vẫn chạy được
        }
    }

    /**
     * Get the view used to render HTTP exceptions.
     * Override để xử lý khi View service chưa được đăng ký
     *
     * @param  \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface  $e
     * @return string|null
     */
    protected function getHttpExceptionView($e)
    {
        try {
            // Kiểm tra xem View service đã được đăng ký chưa
            if (!$this->container->bound('view')) {
                return null;
            }
            return parent::getHttpExceptionView($e);
        } catch (\Exception $exception) {
            // Nếu có lỗi khi gọi view(), trả về null để dùng response mặc định
            return null;
        }
    }

    /**
     * Render exception content using Symfony.
     * Override để xử lý khi APP_DEBUG là null
     *
     * @param  \Throwable  $e
     * @return string
     */
    protected function renderExceptionContent(Throwable $e)
    {
        try {
            $debug = $this->container->make('config')->get('app.debug', false);
            // Đảm bảo $debug luôn là boolean
            $debug = (bool) $debug;
            return $this->renderExceptionWithSymfony($e, $debug);
        } catch (\Exception $exception) {
            // Nếu có lỗi, dùng debug = false
            return $this->renderExceptionWithSymfony($e, false);
        }
    }

    /**
     * Render exception with Symfony ErrorRenderer.
     * Override để đảm bảo $debug luôn là boolean
     *
     * @param  \Throwable  $e
     * @param  bool  $debug
     * @return string
     */
    protected function renderExceptionWithSymfony(Throwable $e, $debug)
    {
        // Đảm bảo $debug luôn là boolean, không phải null
        $debug = (bool) $debug;
        return parent::renderExceptionWithSymfony($e, $debug);
    }
}

