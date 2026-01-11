<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model cần thiết
use App\Models\VPS; // Model quản lý gói VPS
use App\Models\Settings; // Model quản lý cài đặt hệ thống
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class VPSController
 * Controller xử lý hiển thị danh sách VPS cho người dùng
 */
class VPSController extends Controller
{
    /**
     * Hiển thị danh sách VPS (frontend)
     * Lấy tất cả gói VPS và hiển thị trên trang public
     * 
     * @return \Illuminate\View\View - View danh sách VPS
     */
    public function index()
    {
        // Lấy tất cả gói VPS từ database, sắp xếp theo ID giảm dần (mới nhất trước)
        $vpss = VPS::orderBy('id', 'desc')->get();
        // Lấy cài đặt hệ thống (để hiển thị thông tin website)
        $settings = Settings::getOne();
        // Trả về view với dữ liệu VPS và settings
        return view('pages.vps', compact('vpss', 'settings'));
    }
}

