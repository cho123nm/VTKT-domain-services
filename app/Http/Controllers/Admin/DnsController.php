<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model cần thiết
use App\Models\History; // Model lưu lịch sử mua domain
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class DnsController
 * Controller xử lý quản lý yêu cầu cập nhật DNS trong admin panel
 */
class DnsController extends Controller
{
    /**
     * Hiển thị danh sách domain có yêu cầu cập nhật DNS (ahihi = 1)
     * Lấy tất cả domain có ahihi = 1 (đang chờ admin duyệt cập nhật DNS)
     * 
     * @return \Illuminate\View\View - View danh sách domain chờ duyệt DNS
     */
    public function index()
    {
        // Lấy tất cả domain có ahihi = 1 (đang chờ duyệt cập nhật DNS)
        // Eager load relationship user để tránh N+1 query
        $domains = History::where('ahihi', '1')
            ->with('user') // Load thông tin user
            ->orderBy('id', 'desc') // Sắp xếp theo ID giảm dần (mới nhất trước)
            ->get();

        // Trả về view với dữ liệu domains
        return view('admin.dns.index', compact('domains'));
    }

    /**
     * Cập nhật DNS cho domain
     * Admin cập nhật nameserver và trạng thái cho domain
     * 
     * @param Request $request - HTTP request chứa ns1, ns2, trangthai
     * @param int $id - ID của domain cần cập nhật DNS
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách DNS với thông báo
     */
    public function update(Request $request, $id)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'ns1' => 'required|string|max:255', // Nameserver 1 bắt buộc, tối đa 255 ký tự
            'ns2' => 'required|string|max:255', // Nameserver 2 bắt buộc, tối đa 255 ký tự
            'trangthai' => 'required|in:1,2,3,4' // Trạng thái bắt buộc, chỉ nhận 1, 2, 3, 4
        ]);

        // Tìm domain theo ID, nếu không tìm thấy thì throw 404
        $domain = History::findOrFail($id);

        // Cập nhật DNS và reset ahihi về 0 (đã duyệt)
        $domain->ns1 = $request->ns1; // Cập nhật NS1
        $domain->ns2 = $request->ns2; // Cập nhật NS2
        $domain->ahihi = '0'; // Reset ahihi về 0 (đã duyệt)
        $domain->status = $request->trangthai; // Cập nhật trạng thái
        $domain->save(); // Lưu vào database

        // Redirect về danh sách DNS với thông báo thành công
        return redirect()->route('admin.dns.index')
            ->with('success', 'Cập Nhật DNS Thành Công!');
    }

    /**
     * Từ chối yêu cầu cập nhật DNS
     * Admin từ chối yêu cầu cập nhật DNS của user
     * 
     * @param Request $request - HTTP request (không sử dụng trong code này)
     * @param int $id - ID của domain cần từ chối
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách DNS với thông báo
     */
    public function reject(Request $request, $id)
    {
        // Tìm domain theo ID, nếu không tìm thấy thì throw 404
        $domain = History::findOrFail($id);

        // Reset ahihi về 0 và đặt trạng thái thành từ chối (4)
        $domain->ahihi = '0'; // Reset ahihi về 0 (đã xử lý)
        $domain->status = '4'; // Trạng thái: 4 = Từ chối
        $domain->save(); // Lưu vào database

        // Redirect về danh sách DNS với thông báo thành công
        return redirect()->route('admin.dns.index')
            ->with('success', 'Đã Từ Chối Yêu Cầu Cập Nhật DNS!');
    }
}
