<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model cần thiết
use App\Models\Settings; // Model quản lý cài đặt hệ thống
use App\Models\Hosting; // Model quản lý gói hosting
use App\Models\VPS; // Model quản lý gói VPS
use App\Models\HostingHistory; // Model lưu lịch sử mua hosting
use App\Models\VPSHistory; // Model lưu lịch sử mua VPS
use App\Models\User; // Model quản lý người dùng
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class ContactAdminController
 * Controller xử lý trang liên hệ admin sau khi mua hosting/VPS
 */
class ContactAdminController extends Controller
{
    /**
     * Hiển thị trang liên hệ admin sau khi mua hàng
     * Hiển thị thông tin đơn hàng và cách liên hệ admin (Facebook, Zalo)
     * 
     * @param Request $request - HTTP request chứa type và mgd từ query
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // Kiểm tra user đã đăng nhập chưa
        $username = session('users');
        if (!$username) {
            return redirect()->route('login');
        }

        // Tìm user trong database theo username
        $user = User::where('taikhoan', $username)->first();
        // Nếu không tìm thấy user, redirect đến trang đăng nhập
        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy loại đơn hàng và mã giao dịch từ query string
        $type = $request->get('type', ''); // 'hosting' hoặc 'vps'
        $mgd = $request->get('mgd', ''); // Mã giao dịch

        // Nếu thiếu type hoặc mgd, redirect về trang chủ
        if (empty($type) || empty($mgd)) {
            return redirect()->route('home');
        }

        // Lấy thông tin liên hệ từ cài đặt hệ thống
        $settings = Settings::first();
        $contactInfo = [
            'facebook_link' => $settings->facebook_link ?? '', // Link Facebook
            'zalo_phone' => $settings->zalo_phone ?? '' // Số điện thoại Zalo
        ];

        // Khởi tạo biến để lưu thông tin đơn hàng và sản phẩm
        $order = null; // Đơn hàng (HostingHistory hoặc VPSHistory)
        $product = null; // Sản phẩm (Hosting hoặc VPS)
        $productName = ''; // Tên sản phẩm

        // Xử lý theo loại đơn hàng
        if ($type === 'hosting') {
            // Tìm đơn hàng hosting theo mã giao dịch và ID user
            $order = HostingHistory::where('mgd', $mgd)
                ->where('uid', $user->id)
                ->first();
            
            // Nếu tìm thấy đơn hàng, lấy thông tin gói hosting
            if ($order) {
                $product = Hosting::find($order->hosting_id);
                $productName = $product ? $product->name : '';
            }
        } elseif ($type === 'vps') {
            // Tìm đơn hàng VPS theo mã giao dịch và ID user
            $order = VPSHistory::where('mgd', $mgd)
                ->where('uid', $user->id)
                ->first();
            
            // Nếu tìm thấy đơn hàng, lấy thông tin gói VPS
            if ($order) {
                $product = VPS::find($order->vps_id);
                $productName = $product ? $product->name : '';
            }
        }

        // Nếu không tìm thấy đơn hàng hoặc sản phẩm, redirect về trang chủ với thông báo lỗi
        if (!$order || !$product) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng');
        }

        // Trả về view với tất cả dữ liệu cần thiết
        return view('pages.contact-admin', compact('order', 'product', 'productName', 'type', 'mgd', 'contactInfo'));
    }
}

