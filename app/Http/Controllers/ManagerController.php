<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model cần thiết
use App\Models\History; // Model lưu lịch sử mua domain
use App\Models\HostingHistory; // Model lưu lịch sử mua hosting
use App\Models\VPSHistory; // Model lưu lịch sử mua VPS
use App\Models\SourceCodeHistory; // Model lưu lịch sử mua source code
use App\Models\User; // Model quản lý người dùng
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class ManagerController
 * Controller xử lý trang quản lý tất cả dịch vụ đã mua của user
 */
class ManagerController extends Controller
{
    /**
     * Hiển thị tất cả dịch vụ đã mua của user
     * Bao gồm: domain, hosting, VPS, source code
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        // Lấy username từ session
        $username = session('users');
        // Tìm user trong database theo username
        $user = User::where('taikhoan', $username)->first();

        // Nếu không tìm thấy user, redirect đến trang đăng nhập với thông báo lỗi
        if (!$user) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin người dùng!');
        }

        // Lấy đơn hàng domain từ bảng History
        $domainOrders = History::where('uid', $user->id)
            ->orderBy('id', 'desc') // Sắp xếp theo ID giảm dần (mới nhất trước)
            ->get();

        // Lấy đơn hàng hosting từ bảng HostingHistory (kèm thông tin gói hosting)
        $hostingOrders = HostingHistory::where('uid', $user->id)
            ->with('hosting') // Eager load relationship với Hosting để tránh N+1 query
            ->orderBy('id', 'desc')
            ->get();

        // Lấy đơn hàng VPS từ bảng VPSHistory (kèm thông tin gói VPS)
        $vpsOrders = VPSHistory::where('uid', $user->id)
            ->with('vps') // Eager load relationship với VPS để tránh N+1 query
            ->orderBy('id', 'desc')
            ->get();

        // Lấy đơn hàng source code từ bảng SourceCodeHistory (kèm thông tin source code)
        $sourceCodeOrders = SourceCodeHistory::where('uid', $user->id)
            ->with('sourceCode') // Eager load relationship với SourceCode để tránh N+1 query
            ->orderBy('id', 'desc')
            ->get();

        // Trả về view với tất cả dữ liệu đơn hàng
        return view('pages.manager', compact(
            'user', // Thông tin user
            'domainOrders', // Đơn hàng domain
            'hostingOrders', // Đơn hàng hosting
            'vpsOrders', // Đơn hàng VPS
            'sourceCodeOrders' // Đơn hàng source code
        ));
    }
}
