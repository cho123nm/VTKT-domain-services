<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model cần thiết
use App\Models\Hosting; // Model quản lý gói hosting
use App\Models\Settings; // Model quản lý cài đặt hệ thống
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class HostingController
 * Controller xử lý hiển thị danh sách hosting cho người dùng
 */
class HostingController extends Controller
{
    /**
     * Hiển thị danh sách hosting (frontend)
     * Lấy tất cả gói hosting và hiển thị trên trang public
     * 
     * @return \Illuminate\View\View - View danh sách hosting
     */
    public function index()
    {
        // Lấy tất cả gói hosting từ database, sắp xếp theo ID giảm dần (mới nhất trước)
        $hostings = Hosting::orderBy('id', 'desc')->get();
        // Lấy cài đặt hệ thống (để hiển thị thông tin website)
        $settings = Settings::getOne();
        // Trả về view với dữ liệu hosting và settings
        return view('pages.hosting', compact('hostings', 'settings'));
    }
}

