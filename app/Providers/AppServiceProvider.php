<?php
// Khai báo namespace cho ServiceProvider này - thuộc App\Providers
namespace App\Providers;

// Import ServiceProvider base class và các Facade, Model cần thiết
use Illuminate\Support\ServiceProvider; // Base class cho service provider
use Illuminate\Support\Facades\View; // Facade để chia sẻ dữ liệu với views
use App\Models\Settings; // Model quản lý cài đặt hệ thống
use App\Models\User; // Model quản lý người dùng
use App\Models\Feedback; // Model quản lý feedback

/**
 * Class AppServiceProvider
 * Service Provider chính của ứng dụng
 * Chịu trách nhiệm chia sẻ dữ liệu với tất cả views (settings, user info, etc.)
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Đăng ký các service của ứng dụng
     * Phương thức này được gọi khi ứng dụng khởi động
     * 
     * @return void
     */
    public function register(): void
    {
        // Hiện tại không có service nào cần đăng ký
    }

    /**
     * Khởi động các service của ứng dụng
     * Phương thức này được gọi sau khi tất cả service providers đã được đăng ký
     * 
     * @return void
     */
    public function boot(): void
    {
        // Force HTTPS nếu APP_URL là https
        if (env('APP_URL', '')->startsWith('https://')) {
            \URL::forceScheme('https');
            if (!request()->secure() && !request()->header('X-Forwarded-Proto')) {
                request()->server->set('HTTPS', 'on');
            }
        }
        
        // Chia sẻ settings và thông tin user với tất cả views
        // View::composer('*') nghĩa là áp dụng cho tất cả views
        View::composer('*', function ($view) {
            // Lấy cài đặt hệ thống từ database
            $settings = Settings::getOne();
            // Nếu có settings, chuyển thành mảng; nếu không, dùng giá trị mặc định
            $CaiDatChung = $settings ? $settings->toArray() : [
                'tieude' => 'CloudStoreVN', // Tiêu đề mặc định
                'mota' => 'Cung cấp tên miền giá rẻ', // Mô tả mặc định
                'keywords' => 'tên miền, domain, giá rẻ', // Từ khóa mặc định
                'theme' => 'light' // Theme mặc định
            ];
            
            // Khởi tạo các biến mặc định
            $username = 'Không Xác Định'; // Username mặc định
            $sodu = 0; // Số dư mặc định
            $email = '2431540219@vaa.edu.vn'; // Email mặc định
            $unreadMessageCount = 0; // Số tin nhắn chưa đọc mặc định
            
            // Kiểm tra user đã đăng nhập chưa (có session 'users')
            if (session('users')) {
                // Tìm user trong database theo username trong session
                $user = User::findByUsername(session('users'));
                if ($user) {
                    // Lấy thông tin user từ database
                    $username = $user->taikhoan ?? 'Không Xác Định'; // Username từ database
                    $sodu = (int)($user->tien ?? 0); // Số dư từ database (ép kiểu về int)
                    $email = $user->email ?? '2431540219@vaa.edu.vn'; // Email từ database
                    
                    // Đếm số tin nhắn chưa đọc (feedback có status = 0)
                    $unreadMessageCount = Feedback::where('uid', $user->id) // Tìm feedback của user này
                        ->where('status', 0) // Chỉ lấy feedback chưa đọc (status = 0)
                        ->count(); // Đếm số lượng
                }
            }
            
            // Chia sẻ các biến với view
            $view->with([
                'CaiDatChung' => $CaiDatChung, // Cài đặt hệ thống
                'username' => $username, // Username của user đã đăng nhập
                'sodu' => $sodu, // Số dư của user đã đăng nhập
                'email' => $email, // Email của user đã đăng nhập
                'unreadMessageCount' => $unreadMessageCount // Số tin nhắn chưa đọc
            ]);
        });
    }
}

