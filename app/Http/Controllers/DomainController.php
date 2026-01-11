<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model và Service cần thiết
use App\Models\Domain; // Model quản lý thông tin domain
use App\Models\History; // Model lưu lịch sử mua domain
use App\Models\User; // Model quản lý người dùng
use App\Services\DomainService; // Service xử lý logic domain (WHOIS, etc.)
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Session; // Facade để làm việc với session
use Illuminate\Support\Facades\DB; // Facade để thao tác database

/**
 * Class DomainController
 * Controller xử lý các thao tác liên quan đến domain: checkout, mua, quản lý DNS
 */
class DomainController extends Controller
{
    // Thuộc tính lưu trữ instance của DomainService
    protected $domainService;

    /**
     * Hàm khởi tạo (Constructor)
     * Dependency Injection: Laravel tự động inject DomainService vào đây
     * 
     * @param DomainService $domainService - Service để xử lý logic domain
     */
    public function __construct(DomainService $domainService)
    {
        // Gán DomainService vào thuộc tính của class
        $this->domainService = $domainService;
    }

    /**
     * Tạo mã giao dịch (MGD) duy nhất
     * MGD = Mã Giao Dịch - dùng để theo dõi các đơn hàng
     * 
     * @return string - Mã giao dịch dạng chuỗi
     */
    private function generateMGD()
    {
        // Vòng lặp do-while: tạo mã cho đến khi mã không trùng với mã nào trong database
        do {
            // Tạo mã = timestamp hiện tại + số ngẫu nhiên từ 1000-9999
            $mgd = time() . rand(1000, 9999);
        } while (
            // Kiểm tra mã có trùng trong các bảng lịch sử không
            \App\Models\History::where('mgd', $mgd)->exists() || // Kiểm tra trong bảng domain history
            \App\Models\HostingHistory::where('mgd', $mgd)->exists() || // Kiểm tra trong bảng hosting history
            \App\Models\VPSHistory::where('mgd', $mgd)->exists() || // Kiểm tra trong bảng VPS history
            \App\Models\SourceCodeHistory::where('mgd', $mgd)->exists() // Kiểm tra trong bảng source code history
        );
        // Ép kiểu về string và trả về
        return (string)$mgd;
    }
    
    /**
     * Trang checkout domain (legacy method)
     * Hiển thị trang thanh toán domain
     * 
     * @param Request $request - HTTP request chứa domain từ query
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function checkout(Request $request)
    {
        $domain = $request->get('domain', '');
        
        if (empty($domain)) {
            return redirect()->route('home')->with('error', 'Vui lòng chọn tên miền');
        }

        $explode = explode(".", $domain);
        $duoimien = isset($explode[1]) ? '.'.$explode[1] : '';
        
        $domainInfo = Domain::where('duoi', $duoimien)->first();
        
        if (!$domainInfo || $domainInfo->duoi != $duoimien) {
            return redirect()->route('home')->with('error', 'Đuôi miền không hợp lệ');
        }

        return view('pages.checkout', [
            'domain' => $domain,
            'domainInfo' => $domainInfo,
            'tienphaitra' => $domainInfo->price,
            'images' => $domainInfo->image
        ]);
    }

    /**
     * Mua domain (AJAX)
     */
    public function buy(Request $request)
    {
        $request->validate([
            'domain' => 'required|string',
            'ns1' => 'required|string',
            'ns2' => 'required|string',
            'hsd' => 'required|in:1'
        ]);

        if (!Session::has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thực hiện!',
                'html' => '<script>toastr.error("Vui Lòng Đăng Nhập Để Thực Hiện!", "Thông Báo");</script>'
            ]);
        }

        $domain = $request->domain;
        $ns1 = $request->ns1;
        $ns2 = $request->ns2;
        $hsd = $request->hsd;
        $mgd = $this->generateMGD();

        // Lấy đuôi miền
        $explode = explode(".", $domain);
        $duoimien = isset($explode[1]) ? '.'.$explode[1] : '';

        $domainInfo = Domain::where('duoi', $duoimien)->first();

        if (!$domainInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin đuôi miền!',
                'html' => '<script>toastr.error("Không tìm thấy thông tin đuôi miền!", "Thông Báo");</script>'
            ]);
        }

        $tienphaitra = $domainInfo->price;

        // Kiểm tra domain đã mua chưa
        $checkls = History::where('domain', $domain)->first();

        if ($checkls) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng này đã thanh toán, chờ xử lí!',
                'html' => '<script>toastr.error("Đơn Hàng Này Đã Thanh Toán, Chờ Xử Lí!", "Thông Báo");</script>'
            ]);
        }

        $user = User::where('taikhoan', Session::get('users'))->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!',
                'html' => '<script>toastr.error("Không tìm thấy thông tin tài khoản!", "Thông Báo");</script>'
            ]);
        }

        if ($user->tien < $tienphaitra) {
            return response()->json([
                'success' => false,
                'message' => 'Số dư tài khoản không đủ!',
                'html' => '<script>toastr.error("Số Dư Tài Khoản Không Đủ!", "Thông Báo");</script>'
            ]);
        }

        // Tạo đơn hàng
        $time = date('Y-m-d H:i:s');
        
        DB::beginTransaction();
        try {
            $history = History::create([
                'uid' => $user->id,
                'domain' => $domain,
                'ns1' => $ns1,
                'ns2' => $ns2,
                'hsd' => (int)$hsd,
                'status' => 0,
                'mgd' => (string)$mgd,
                'time' => $time,
                'timedns' => '0',
                'ahihi' => 0
            ]);

            // Trừ tiền
            $user->incrementBalance(-1 * (int)$tienphaitra);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mua tên miền thành công, chờ xử lí!',
                'html' => '<script>toastr.success("Mua Tên Miền Thành Công, Chờ Xử Lí!", "Thông Báo");</script>'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Không thể mua vào lúc này!',
                'html' => '<script>toastr.error("Không Thể Mua Vào Lúc Này!", "Thông Báo");</script>'
            ]);
        }
    }

    /**
     * Trang quản lý domain
     */
    public function manage(Request $request)
    {
        if (!Session::has('users')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $user = User::where('taikhoan', Session::get('users'))->first();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin người dùng');
        }

        $domains = History::where('uid', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.manage-domains', [
            'domains' => $domains
        ]);
    }

    /**
     * Show domain management page for a specific domain
     * 
     * @param int $id Domain history ID
     * @return \Illuminate\View\View
     */
    public function manageDomain($id)
    {
        if (!Session::has('users')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $user = User::where('taikhoan', Session::get('users'))->first();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin người dùng');
        }

        // Validate user owns the domain
        $domainHistory = History::where('id', $id)
            ->where('uid', $user->id)
            ->first();

        if (!$domainHistory) {
            return redirect()->route('manager.index')->with('error', 'Bạn không có quyền quản lý miền này!');
        }

        if ($domainHistory->status == 4) {
            return redirect()->route('manager.index')->with('error', 'Tên miền này đã bị từ chối hỗ trợ!');
        }

        return view('pages.manage-domain', [
            'domainHistory' => $domainHistory
        ]);
    }

    /**
     * Trang quản lý DNS của một domain (legacy method)
     */
    public function manageDns(Request $request)
    {
        if (!Session::has('users')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $user = User::where('taikhoan', Session::get('users'))->first();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin người dùng');
        }

        $mgd = $request->get('domain', '');
        $domainHistory = History::where('uid', $user->id)
            ->where('mgd', $mgd)
            ->first();

        if (!$domainHistory) {
            return redirect()->route('domain.manage')->with('error', 'Bạn không có quyền quản lý miền này!');
        }

        if ($domainHistory->status == 4) {
            return redirect()->route('domain.manage')->with('error', 'Tên miền này đã bị từ chối hỗ trợ!');
        }

        return view('pages.manage-dns', [
            'domainHistory' => $domainHistory
        ]);
    }

    /**
     * Update DNS records for a domain
     * 
     * @param Request $request
     * @param int $id Domain history ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDns(Request $request, $id = null)
    {
        if (!Session::has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập',
                'html' => '<script>toastr.error("Vui Lòng Đăng Nhập!", "Thông Báo");</script>'
            ]);
        }

        $request->validate([
            'ns1' => 'required|string',
            'ns2' => 'required|string'
        ]);

        // Support both ID from route and MGD from request (for backward compatibility)
        $mgd = $request->mgd ?? null;

        $user = User::where('taikhoan', Session::get('users'))->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng',
                'html' => '<script>toastr.error("Không Tìm Thấy Thông Tin Người Dùng!", "Thông Báo");</script>'
            ]);
        }

        // Validate user owns the domain
        if ($id) {
            $domainHistory = History::where('id', $id)
                ->where('uid', $user->id)
                ->first();
        } elseif ($mgd) {
            $domainHistory = History::where('mgd', $mgd)
                ->where('uid', $user->id)
                ->first();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu thông tin domain',
                'html' => '<script>toastr.error("Thiếu Thông Tin Domain!", "Thông Báo");</script>'
            ]);
        }

        if (!$domainHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền quản lý miền này!',
                'html' => '<script>toastr.error("Bạn Không Có Quyền Quản Lý Miền Này!", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra thời gian cập nhật DNS (15 ngày)
        $today = date('d/m/Y');
        if ($domainHistory->timedns != '0' && $domainHistory->timedns == $today) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ có thể thay đổi DNS sau 15 ngày kể từ lần cập nhật gần nhất!',
                'html' => '<script>toastr.error("Bạn Chỉ Có Thể Thay Đổi DNS Sau 15 Ngày Kể Từ Lần Cập Nhật Gần Nhất!", "Thông Báo");</script>'
            ]);
        }

        $time = date('d/m/Y - H:i:s');
        $timedns = date('d/m/Y');

        $domainHistory->ns1 = $request->ns1;
        $domainHistory->ns2 = $request->ns2;
        $domainHistory->ahihi = 1;
        $domainHistory->status = 3; // Chờ duyệt
        $domainHistory->timedns = $timedns;
        $domainHistory->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật DNS thành công, chờ admin duyệt!',
            'html' => '<script>toastr.success("Cập Nhật DNS Thành Công, Chờ Admin Duyệt!", "Thông Báo");</script>'
        ]);
    }
}

