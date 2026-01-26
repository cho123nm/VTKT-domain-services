<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model cần thiết
use App\Models\Domain; // Model quản lý thông tin domain
use App\Models\Settings; // Model quản lý cài đặt hệ thống
use App\Models\User; // Model quản lý người dùng
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class HomeController
 * Controller xử lý trang chủ của website
 */
class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ
     * Lấy danh sách domain, cài đặt hệ thống và thông tin user (nếu đã đăng nhập)
     * 
     * @return \Illuminate\View\View - View trang chủ với dữ liệu cần thiết
     */
    public function index()
    {
        // Lấy tất cả các loại domain từ database
        $domains = Domain::all();
        
        // Lấy cài đặt hệ thống từ database (tiêu đề, mô tả, keywords)
        $settings = Settings::getOne();
        
        // Nếu không có cài đặt, sử dụng giá trị mặc định
        if (!$settings) {
            $settings = (object)[
                'tieude' => 'CloudStoreVN', // Tiêu đề website
                'mota' => 'Cung cấp tên miền giá rẻ', // Mô tả website
                'keywords' => 'tên miền, domain, giá rẻ' // Từ khóa SEO
            ];
        }
        
        // Khởi tạo biến thông tin user (mặc định cho user chưa đăng nhập)
        $user = null; // Đối tượng User (null nếu chưa đăng nhập)
        $username = 'Không Xác Định'; // Tên đăng nhập (mặc định)
        $sodu = 0; // Số dư tài khoản (mặc định 0)
        $email = ''; // Email (mặc định rỗng)
        
        // Kiểm tra user đã đăng nhập chưa (kiểm tra session có key 'users' không)
        if (session()->has('users')) {
            // Tìm thông tin user trong database theo username trong session
            $user = User::findByUsername(session('users'));
            
            // Nếu tìm thấy user, lấy thông tin chi tiết
            if ($user) {
                $username = $user->taikhoan; // Tên đăng nhập
                $sodu = $user->tien; // Số dư tài khoản
                $email = $user->email; // Email
            }
        }
        
        // Trả về view trang chủ với tất cả dữ liệu cần thiết
        // compact() tạo mảng từ các biến: ['domains' => $domains, 'settings' => $settings, ...]
        return view('pages.home', compact('domains', 'settings', 'user', 'username', 'sodu', 'email'));
    }

    /**
     * Hiển thị trang giới thiệu (About)
     * 
     * @return \Illuminate\View\View - View trang giới thiệu
     */
    public function about()
    {
        return view('pages.about');
    }
}
