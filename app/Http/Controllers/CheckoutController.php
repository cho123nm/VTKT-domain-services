<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model cần thiết để xử lý dữ liệu
use App\Models\Domain; // Model quản lý thông tin domain
use App\Models\History; // Model lưu lịch sử mua domain
use App\Models\Hosting; // Model quản lý gói hosting
use App\Models\HostingHistory; // Model lưu lịch sử mua hosting
use App\Models\VPS; // Model quản lý gói VPS
use App\Models\VPSHistory; // Model lưu lịch sử mua VPS
use App\Models\SourceCode; // Model quản lý source code
use App\Models\SourceCodeHistory; // Model lưu lịch sử mua source code
use App\Models\User; // Model quản lý người dùng
use App\Services\TelegramService; // Service gửi thông báo Telegram
use App\Mail\OrderConfirmationMail; // Class gửi email xác nhận đơn hàng
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\DB; // Facade để thao tác database
use Illuminate\Support\Facades\Mail; // Facade để gửi email
use Illuminate\Support\Facades\Log; // Facade để ghi log

/**
 * Class CheckoutController
 * Controller xử lý các thao tác thanh toán và checkout cho domain, hosting, VPS, source code
 */
class CheckoutController extends Controller
{
    // Thuộc tính lưu trữ instance của TelegramService để gửi thông báo
    protected $telegramService;

    /**
     * Hàm khởi tạo (Constructor)
     * Dependency Injection: Laravel tự động inject TelegramService vào đây
     * 
     * @param TelegramService $telegramService - Service để gửi thông báo Telegram
     */
    public function __construct(TelegramService $telegramService)
    {
        // Gán TelegramService vào thuộc tính của class
        $this->telegramService = $telegramService;
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
            // Ví dụ: 1703123456 + 5678 = "17031234565678"
            $mgd = time() . rand(1000, 9999);
        } while (
            // Kiểm tra mã có trùng trong các bảng lịch sử không
            History::where('mgd', $mgd)->exists() || // Kiểm tra trong bảng domain history
            HostingHistory::where('mgd', $mgd)->exists() || // Kiểm tra trong bảng hosting history
            VPSHistory::where('mgd', $mgd)->exists() || // Kiểm tra trong bảng VPS history
            SourceCodeHistory::where('mgd', $mgd)->exists() // Kiểm tra trong bảng source code history
        );
        // Ép kiểu về string và trả về
        return (string)$mgd;
    }
    
    /**
     * Hiển thị trang checkout cho domain
     * 
     * @param Request $request - HTTP request chứa thông tin domain từ URL query
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function domain(Request $request)
    {
        // Lấy tên domain từ query string (?domain=example.com)
        $domainName = $request->query('domain');
        
        // Nếu không có domain trong URL, chuyển về trang chủ
        if (empty($domainName)) {
            return redirect()->route('home');
        }

        // Tách tên domain thành các phần bằng dấu chấm
        // Ví dụ: "example.com" -> ["example", "com"]
        $parts = explode('.', $domainName);
        
        // Nếu không đủ 2 phần (tên + đuôi), chuyển về trang chủ
        if (count($parts) < 2) {
            return redirect()->route('home');
        }
        
        // Tạo đuôi domain (ví dụ: ".com")
        $extension = '.' . $parts[1];
        
        // Tìm thông tin loại domain trong database theo đuôi
        $domain = Domain::findByDuoi($extension);
        
        // Nếu không tìm thấy loại domain, chuyển về trang chủ
        if (!$domain) {
            return redirect()->route('home');
        }

        // Trả về view checkout domain với dữ liệu cần thiết
        return view('pages.checkout.domain', [
            'domainName' => $domainName, // Tên domain đầy đủ
            'domain' => $domain, // Thông tin loại domain từ database
            'price' => $domain->price // Giá domain
        ]);
    }

    /**
     * Hiển thị trang checkout cho hosting
     * 
     * @param Request $request - HTTP request chứa ID hosting từ URL query
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function hosting(Request $request)
    {
        // Kiểm tra người dùng đã đăng nhập chưa (kiểm tra session 'users')
        if (!session()->has('users')) {
            // Nếu chưa đăng nhập, chuyển đến trang đăng nhập
            return redirect()->route('login');
        }

        // Lấy ID hosting từ query string (?id=1), mặc định là 0 nếu không có
        $id = $request->query('id', 0);
        
        // Nếu ID = 0 hoặc không hợp lệ, chuyển về trang danh sách hosting
        if ($id == 0) {
            return redirect()->route('hosting.index');
        }

        // Tìm gói hosting trong database theo ID
        $hosting = Hosting::find($id);
        
        // Nếu không tìm thấy gói hosting, chuyển về trang danh sách
        if (!$hosting) {
            return redirect()->route('hosting.index');
        }

        // Trả về view checkout hosting với thông tin gói hosting
        return view('pages.checkout.hosting', [
            'hosting' => $hosting
        ]);
    }

    /**
     * Hiển thị trang checkout cho VPS
     * 
     * @param Request $request - HTTP request chứa ID VPS từ URL query
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function vps(Request $request)
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        // Lấy ID VPS từ query string, mặc định là 0
        $id = $request->query('id', 0);
        
        // Nếu ID không hợp lệ, chuyển về trang danh sách VPS
        if ($id == 0) {
            return redirect()->route('vps.index');
        }

        // Tìm gói VPS trong database theo ID
        $vps = VPS::find($id);
        
        // Nếu không tìm thấy, chuyển về trang danh sách
        if (!$vps) {
            return redirect()->route('vps.index');
        }

        // Trả về view checkout VPS với thông tin gói VPS
        return view('pages.checkout.vps', [
            'vps' => $vps
        ]);
    }

    /**
     * Hiển thị trang checkout cho source code
     * 
     * @param Request $request - HTTP request chứa ID source code từ URL query
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function sourcecode(Request $request)
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        // Lấy ID source code từ query string, mặc định là 0
        $id = $request->query('id', 0);
        
        // Nếu ID không hợp lệ, chuyển về trang danh sách source code
        if ($id == 0) {
            return redirect()->route('sourcecode.index');
        }

        // Tìm source code trong database theo ID
        $sourceCode = SourceCode::find($id);
        
        // Nếu không tìm thấy, chuyển về trang danh sách
        if (!$sourceCode) {
            return redirect()->route('sourcecode.index');
        }

        // Trả về view checkout source code với thông tin sản phẩm
        return view('pages.checkout.sourcecode', [
            'sourceCode' => $sourceCode
        ]);
    }

    /**
     * Xử lý mua domain (process domain purchase)
     * Hàm này được gọi từ AJAX request khi người dùng submit form mua domain
     * 
     * @param Request $request - HTTP request chứa thông tin domain, NS1, NS2, HSD
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function processDomain(Request $request)
    {
        // Kiểm tra xem request có session không (quan trọng cho AJAX)
        if (!$request->hasSession()) {
            // Ghi log cảnh báo nếu không có session
            Log::warning('ProcessDomain - No session available', [
                'url' => $request->fullUrl(), // URL đầy đủ của request
                'cookie_header' => $request->header('Cookie') // Header Cookie từ request
            ]);
            
            // Trả về JSON response báo lỗi cho AJAX
            return response()->json([
                'success' => false,
                'message' => 'Phiên làm việc đã hết hạn, vui lòng tải lại trang và đăng nhập lại!'
            ]);
        }
        
        // Ghi log để debug - đảm bảo request đã đến được controller
        Log::info('ProcessDomain - Request received', [
            'method' => $request->method(), // HTTP method (GET, POST, etc.)
            'path' => $request->path(), // Đường dẫn URL
            'url' => $request->fullUrl(), // URL đầy đủ
            'ip' => $request->ip(), // IP của người dùng
            'has_cookie' => $request->hasHeader('Cookie'), // Có Cookie header không
            'cookie_header' => $request->header('Cookie') ? 'present' : 'missing', // Cookie có hay không
            'has_session' => $request->hasSession(), // Có session không
            'session_id' => $request->session()->getId() // ID của session
        ]);
        
        // Lấy thông tin session để kiểm tra đăng nhập
        // Lấy giá trị 'users' từ session (username của người dùng)
        $sessionUsers = $request->session()->get('users');
        // Kiểm tra session có key 'users' không
        $hasUsers = $request->session()->has('users');
        // Lấy tất cả dữ liệu trong session để debug
        $allSession = $request->session()->all();
        // Lấy ID của session
        $sessionId = $request->session()->getId();
        
        // Ghi log thông tin session để debug
        Log::info('ProcessDomain - Session check', [
            'has_users' => $hasUsers, // Có key 'users' trong session không
            'users_value' => $sessionUsers, // Giá trị của 'users' (username)
            'session_id' => $sessionId, // ID session
            'session_keys' => array_keys($allSession), // Tất cả keys trong session
            'cookie_header' => $request->header('Cookie'), // Cookie header
            'user_agent' => $request->header('User-Agent') // User agent của browser
        ]);
        
        // Kiểm tra người dùng đã đăng nhập chưa (kiểm tra cả giá trị và sự tồn tại)
        if (empty($sessionUsers) && !$hasUsers) {
            // Ghi log cảnh báo nếu chưa đăng nhập
            Log::warning('ProcessDomain - User not logged in', [
                'session_id' => $sessionId,
                'all_session_keys' => array_keys($allSession), // Tất cả keys trong session
                'cookie_header' => $request->header('Cookie'),
                'session_driver' => config('session.driver') // Driver session đang dùng (file, database, etc.)
            ]);
            
            // Trả về JSON response báo lỗi và script redirect đến trang đăng nhập
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!',
                'html' => '<script>
                    toastr.error("Vui Lòng Đăng Nhập Để Thực Hiện!", "Thông Báo");
                    setTimeout(function() {
                        window.location.href = "' . route('login') . '";
                    }, 2000);
                </script>'
            ]);
        }

        // Validate dữ liệu đầu vào từ form
        // Kiểm tra các trường bắt buộc: domain, ns1, ns2, hsd
        $request->validate([
            'domain' => 'required|string', // Tên domain bắt buộc, kiểu string
            'ns1' => 'required|string', // Nameserver 1 bắt buộc
            'ns2' => 'required|string', // Nameserver 2 bắt buộc
            'hsd' => 'required|string' // Hạn sử dụng bắt buộc
        ]);

        // Lấy dữ liệu từ request sau khi validate
        $domain = $request->input('domain'); // Tên domain
        $ns1 = $request->input('ns1'); // Nameserver 1
        $ns2 = $request->input('ns2'); // Nameserver 2
        $hsd = $request->input('hsd'); // Hạn sử dụng
        
        // Tạo mã giao dịch duy nhất
        $mgd = $this->generateMGD();

        // Tách tên domain để lấy đuôi (extension)
        // Ví dụ: "example.com" -> ["example", "com"]
        $parts = explode('.', $domain);
        
        // Kiểm tra domain có đủ phần không (ít nhất phải có tên và đuôi)
        if (count($parts) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Tên miền không hợp lệ!'
            ]);
        }
        
        // Tạo đuôi domain (ví dụ: ".com")
        $extension = '.' . $parts[1];
        
        // Tìm thông tin loại domain trong database theo đuôi
        $domainType = Domain::findByDuoi($extension);
        
        // Nếu không tìm thấy loại domain, trả về lỗi
        if (!$domainType) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin đuôi miền!'
            ]);
        }

        // Lấy giá domain từ loại domain đã tìm được
        $price = $domainType->price;

        // Tìm thông tin người dùng từ database theo username trong session
        $user = User::findByUsername($sessionUsers);
        
        // Nếu không tìm thấy user nhưng session vẫn có 'users', thử lấy lại session
        if (!$user && $sessionUsers) {
            // Lấy lại username từ session (có thể session đã được refresh)
            $sessionUsers = $request->session()->get('users');
            // Tìm lại user
            $user = User::findByUsername($sessionUsers);
        }
        
        // Nếu vẫn không tìm thấy user, trả về lỗi
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!'
            ]);
        }

        // Kiểm tra domain đã được mua chưa (tránh trùng lặp)
        $existingOrder = History::where('domain', $domain)->first();
        
        // Nếu đã có đơn hàng với domain này, trả về thông báo
        if ($existingOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn Hàng Này Đã Thanh Toán, Chờ Xử Lí!'
            ]);
        }

        // Kiểm tra số dư tài khoản có đủ để thanh toán không
        if ($user->tien < $price) {
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!'
            ]);
        }

        // Bắt đầu transaction database để đảm bảo tính nhất quán dữ liệu
        try {
            // Bắt đầu transaction - nếu có lỗi sẽ rollback toàn bộ
            DB::beginTransaction();

            // Trừ số dư tài khoản người dùng
            // incrementBalance(-price) = giảm số dư đi một lượng = price
            $user->incrementBalance(-1 * (int)$price);

            // Tạo đơn hàng mới trong bảng History
            $history = new History(); // Tạo instance mới của Model History
            $history->uid = $user->id; // ID người dùng
            $history->domain = $domain; // Tên domain
            $history->ns1 = $ns1; // Nameserver 1
            $history->ns2 = $ns2; // Nameserver 2
            $history->hsd = $hsd; // Hạn sử dụng
            $history->status = 0; // Trạng thái: 0 = Chờ xử lý (Pending)
            $history->mgd = (string)$mgd; // Mã giao dịch
            $history->time = date('Y-m-d H:i:s'); // Thời gian tạo đơn hàng
            $history->timedns = '0'; // Thời gian DNS (mặc định 0)
            $history->save(); // Lưu vào database

            // Commit transaction - xác nhận tất cả thay đổi
            DB::commit();

            // Gửi thông báo Telegram cho admin về đơn hàng mới
            $this->telegramService->notifyNewOrder('domain', [
                'username' => $user->taikhoan, // Username người dùng
                'mgd' => (string)$mgd, // Mã giao dịch
                'domain' => $domain, // Tên domain
                'ns1' => $ns1, // Nameserver 1
                'ns2' => $ns2, // Nameserver 2
                'time' => date('d/m/Y - H:i:s') // Thời gian định dạng Việt Nam
            ]);

            // Gửi email xác nhận đơn hàng cho người dùng (nếu có email)
            if ($user->email) {
                try {
                    // Ghi log khi bắt đầu gửi email
                    Log::info('Sending domain order confirmation email', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'domain' => $domain
                    ]);
                    
                    // Gửi email xác nhận đơn hàng
                    Mail::to($user->email)->send(new OrderConfirmationMail(
                        $history, // Đối tượng History chứa thông tin đơn hàng
                        'domain', // Loại đơn hàng: domain
                        $user, // Đối tượng User
                        [
                            'price' => $price, // Giá domain
                            'domain' => $domain, // Tên domain
                            'ns1' => $ns1, // Nameserver 1
                            'ns2' => $ns2, // Nameserver 2
                        ]
                    ));
                    
                    // Ghi log khi gửi email thành công
                    Log::info('Domain order confirmation email sent successfully', [
                        'user_email' => $user->email,
                        'mgd' => $mgd
                    ]);
                } catch (\Exception $e) {
                    // Nếu có lỗi khi gửi email, chỉ ghi log, không báo lỗi cho user
                    Log::error('Domain order email error', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'error' => $e->getMessage(), // Thông báo lỗi
                        'trace' => $e->getTraceAsString() // Stack trace để debug
                    ]);
                    // Không báo lỗi cho user, chỉ log - vì đơn hàng đã được tạo thành công
                }
            } else {
                // Nếu user không có email, ghi log cảnh báo
                Log::warning('Domain order - User has no email', [
                    'user_id' => $user->id,
                    'username' => $user->taikhoan,
                    'mgd' => $mgd
                ]);
            }

            // Trả về JSON response thành công cho AJAX
            return response()->json([
                'success' => true,
                'message' => 'Mua Tên Miền Thành Công, Chờ Xử Lí!'
            ]);

        } catch (\Exception $e) {
            // Nếu có lỗi bất kỳ, rollback transaction (hoàn tác tất cả thay đổi)
            DB::rollBack();
            
            // Trả về JSON response lỗi cho AJAX
            return response()->json([
                'success' => false,
                'message' => 'Không Thể Mua Vào Lúc Này!'
            ]);
        }
    }

    /**
     * Xử lý mua hosting (process hosting purchase)
     * Hàm này được gọi từ AJAX request khi người dùng submit form mua hosting
     * 
     * @param Request $request - HTTP request chứa hosting_id và period (month/year)
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function processHosting(Request $request)
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!'
            ]);
        }

        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'hosting_id' => 'required|integer', // ID hosting bắt buộc, kiểu integer
            'period' => 'required|in:month,year' // Thời hạn bắt buộc, chỉ nhận 'month' hoặc 'year'
        ]);

        // Lấy dữ liệu từ request sau khi validate
        $hostingId = $request->input('hosting_id'); // ID gói hosting
        $period = $request->input('period'); // Thời hạn: 'month' hoặc 'year'
        
        // Tạo mã giao dịch duy nhất
        $mgd = $this->generateMGD();

        // Tìm gói hosting trong database theo ID
        $hosting = Hosting::find($hostingId);
        
        // Nếu không tìm thấy gói hosting, trả về lỗi
        if (!$hosting) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy gói hosting!'
            ]);
        }

        // Xác định giá dựa trên thời hạn: tháng hoặc năm
        $price = $period === 'month' ? $hosting->price_month : $hosting->price_year;

        // Tìm thông tin người dùng từ session
        $user = User::findByUsername(session('users'));
        
        // Nếu không tìm thấy user, trả về lỗi
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!'
            ]);
        }

        // Kiểm tra số dư tài khoản có đủ để thanh toán không
        if ($user->tien < $price) {
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!'
            ]);
        }

        // Bắt đầu transaction database
        try {
            DB::beginTransaction();

            // Trừ số dư tài khoản người dùng
            $user->incrementBalance(-1 * (int)$price);

            // Tạo đơn hàng hosting mới trong bảng HostingHistory
            $history = new HostingHistory(); // Tạo instance mới của Model HostingHistory
            $history->uid = $user->id; // ID người dùng
            $history->hosting_id = $hostingId; // ID gói hosting
            $history->period = $period; // Thời hạn: 'month' hoặc 'year'
            $history->mgd = (string)$mgd; // Mã giao dịch
            $history->status = 1; // Trạng thái: 1 = Đã duyệt ngay (Approved immediately)
            $history->time = date('Y-m-d H:i:s'); // Thời gian tạo đơn hàng
            $history->save(); // Lưu vào database

            // Commit transaction - xác nhận tất cả thay đổi
            DB::commit();

            // Gửi thông báo Telegram cho admin về đơn hàng mới
            $this->telegramService->notifyNewOrder('hosting', [
                'username' => $user->taikhoan, // Username người dùng
                'mgd' => (string)$mgd, // Mã giao dịch
                'package_name' => $hosting->name, // Tên gói hosting
                'period' => $period === 'month' ? '1' : '12', // Thời hạn: 1 tháng hoặc 12 tháng
                'domain' => 'N/A', // Domain chưa có (sẽ cung cấp sau)
                'time' => date('d/m/Y - H:i:s') // Thời gian định dạng Việt Nam
            ]);

            // Gửi email xác nhận đơn hàng cho người dùng (nếu có email)
            if ($user->email) {
                try {
                    // Ghi log khi bắt đầu gửi email
                    Log::info('Sending hosting order confirmation email', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'hosting_id' => $hostingId
                    ]);
                    
                    // Gửi email xác nhận đơn hàng
                    Mail::to($user->email)->send(new OrderConfirmationMail(
                        $history, // Đối tượng HostingHistory chứa thông tin đơn hàng
                        'hosting', // Loại đơn hàng: hosting
                        $user, // Đối tượng User
                        [
                            'price' => $price, // Giá hosting
                            'package_name' => $hosting->name, // Tên gói hosting
                            'period' => $period === 'month' ? '1' : '12', // Thời hạn
                        ]
                    ));
                    
                    // Ghi log khi gửi email thành công
                    Log::info('Hosting order confirmation email sent successfully', [
                        'user_email' => $user->email,
                        'mgd' => $mgd
                    ]);
                } catch (\Exception $e) {
                    // Nếu có lỗi khi gửi email, chỉ ghi log
                    Log::error('Hosting order email error', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'error' => $e->getMessage(), // Thông báo lỗi
                        'trace' => $e->getTraceAsString() // Stack trace để debug
                    ]);
                }
            } else {
                // Nếu user không có email, ghi log cảnh báo
                Log::warning('Hosting order - User has no email', [
                    'user_id' => $user->id,
                    'username' => $user->taikhoan,
                    'mgd' => $mgd
                ]);
            }

            // Trả về JSON response thành công và redirect URL
            return response()->json([
                'success' => true,
                'message' => 'Mua Hosting Thành Công!',
                'redirect' => route('contact-admin', ['type' => 'hosting', 'mgd' => $mgd]) // URL để liên hệ admin
            ]);

        } catch (\Exception $e) {
            // Nếu có lỗi bất kỳ, rollback transaction
            DB::rollBack();
            
            // Trả về JSON response lỗi
            return response()->json([
                'success' => false,
                'message' => 'Không Thể Mua Vào Lúc Này!'
            ]);
        }
    }

    /**
     * Xử lý mua VPS (process VPS purchase)
     * Hàm này được gọi từ AJAX request khi người dùng submit form mua VPS
     * 
     * @param Request $request - HTTP request chứa vps_id và period (month/year)
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function processVPS(Request $request)
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!'
            ]);
        }

        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'vps_id' => 'required|integer', // ID VPS bắt buộc, kiểu integer
            'period' => 'required|in:month,year' // Thời hạn bắt buộc, chỉ nhận 'month' hoặc 'year'
        ]);

        // Lấy dữ liệu từ request sau khi validate
        $vpsId = $request->input('vps_id'); // ID gói VPS
        $period = $request->input('period'); // Thời hạn: 'month' hoặc 'year'
        
        // Tạo mã giao dịch duy nhất
        $mgd = $this->generateMGD();

        // Tìm gói VPS trong database theo ID
        $vps = VPS::find($vpsId);
        
        // Nếu không tìm thấy gói VPS, trả về lỗi
        if (!$vps) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy gói VPS!'
            ]);
        }

        // Xác định giá dựa trên thời hạn: tháng hoặc năm
        $price = $period === 'month' ? $vps->price_month : $vps->price_year;

        // Tìm thông tin người dùng từ session
        $user = User::findByUsername(session('users'));
        
        // Nếu không tìm thấy user, trả về lỗi
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!'
            ]);
        }

        // Kiểm tra số dư tài khoản có đủ để thanh toán không
        if ($user->tien < $price) {
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!'
            ]);
        }

        // Bắt đầu transaction database
        try {
            DB::beginTransaction();

            // Trừ số dư tài khoản người dùng
            $user->incrementBalance(-1 * (int)$price);

            // Tạo đơn hàng VPS mới trong bảng VPSHistory
            $history = new VPSHistory(); // Tạo instance mới của Model VPSHistory
            $history->uid = $user->id; // ID người dùng
            $history->vps_id = $vpsId; // ID gói VPS
            $history->period = $period; // Thời hạn: 'month' hoặc 'year'
            $history->mgd = (string)$mgd; // Mã giao dịch
            $history->status = 1; // Trạng thái: 1 = Đã duyệt ngay (Approved immediately)
            $history->time = date('Y-m-d H:i:s'); // Thời gian tạo đơn hàng
            $history->save(); // Lưu vào database

            // Commit transaction - xác nhận tất cả thay đổi
            DB::commit();

            // Gửi thông báo Telegram cho admin về đơn hàng mới
            $this->telegramService->notifyNewOrder('vps', [
                'username' => $user->taikhoan, // Username người dùng
                'mgd' => (string)$mgd, // Mã giao dịch
                'package_name' => $vps->name, // Tên gói VPS
                'period' => $period === 'month' ? '1' : '12', // Thời hạn: 1 tháng hoặc 12 tháng
                'time' => date('d/m/Y - H:i:s') // Thời gian định dạng Việt Nam
            ]);

            // Gửi email xác nhận đơn hàng cho người dùng (nếu có email)
            if ($user->email) {
                try {
                    // Ghi log khi bắt đầu gửi email
                    Log::info('Sending VPS order confirmation email', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'vps_id' => $vpsId
                    ]);
                    
                    // Gửi email xác nhận đơn hàng
                    Mail::to($user->email)->send(new OrderConfirmationMail(
                        $history, // Đối tượng VPSHistory chứa thông tin đơn hàng
                        'vps', // Loại đơn hàng: vps
                        $user, // Đối tượng User
                        [
                            'price' => $price, // Giá VPS
                            'package_name' => $vps->name, // Tên gói VPS
                            'period' => $period, // Thời hạn: 'month' hoặc 'year'
                        ]
                    ));
                    
                    // Ghi log khi gửi email thành công
                    Log::info('VPS order confirmation email sent successfully', [
                        'user_email' => $user->email,
                        'mgd' => $mgd
                    ]);
                } catch (\Exception $e) {
                    // Nếu có lỗi khi gửi email, chỉ ghi log
                    Log::error('VPS order email error', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'error' => $e->getMessage(), // Thông báo lỗi
                        'trace' => $e->getTraceAsString() // Stack trace để debug
                    ]);
                }
            } else {
                // Nếu user không có email, ghi log cảnh báo
                Log::warning('VPS order - User has no email', [
                    'user_id' => $user->id,
                    'username' => $user->taikhoan,
                    'mgd' => $mgd
                ]);
            }

            // Trả về JSON response thành công và redirect URL
            return response()->json([
                'success' => true,
                'message' => 'Mua VPS Thành Công!',
                'redirect' => route('contact-admin', ['type' => 'vps', 'mgd' => $mgd]) // URL để liên hệ admin
            ]);

        } catch (\Exception $e) {
            // Nếu có lỗi bất kỳ, rollback transaction
            DB::rollBack();
            
            // Trả về JSON response lỗi
            return response()->json([
                'success' => false,
                'message' => 'Không Thể Mua Vào Lúc Này!'
            ]);
        }
    }

    /**
     * Xử lý mua source code (process source code purchase)
     * Hàm này được gọi từ AJAX request khi người dùng submit form mua source code
     * 
     * @param Request $request - HTTP request chứa source_code_id
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function processSourceCode(Request $request)
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!'
            ]);
        }

        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'source_code_id' => 'required|integer' // ID source code bắt buộc, kiểu integer
        ]);

        // Lấy dữ liệu từ request sau khi validate
        $sourceCodeId = $request->input('source_code_id'); // ID source code
        
        // Tạo mã giao dịch duy nhất
        $mgd = $this->generateMGD();

        // Tìm source code trong database theo ID
        $sourceCode = SourceCode::find($sourceCodeId);
        
        // Nếu không tìm thấy source code, trả về lỗi
        if (!$sourceCode) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy source code!'
            ]);
        }

        // Lấy giá source code
        $price = $sourceCode->price;

        // Tìm thông tin người dùng từ session
        $user = User::findByUsername(session('users'));
        
        // Nếu không tìm thấy user, trả về lỗi
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!'
            ]);
        }

        // Kiểm tra số dư tài khoản có đủ để thanh toán không
        if ($user->tien < $price) {
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!'
            ]);
        }

        // Bắt đầu transaction database
        try {
            DB::beginTransaction();

            // Trừ số dư tài khoản người dùng
            $user->incrementBalance(-1 * (int)$price);

            // Tạo đơn hàng source code mới trong bảng SourceCodeHistory
            $history = new SourceCodeHistory(); // Tạo instance mới của Model SourceCodeHistory
            $history->uid = $user->id; // ID người dùng
            $history->source_code_id = $sourceCodeId; // ID source code
            $history->mgd = (string)$mgd; // Mã giao dịch
            $history->status = 1; // Trạng thái: 1 = Đã duyệt ngay (Approved immediately)
            $history->time = date('Y-m-d H:i:s'); // Thời gian tạo đơn hàng
            $history->save(); // Lưu vào database

            // Commit transaction - xác nhận tất cả thay đổi
            DB::commit();

            // Chuẩn bị dữ liệu response trước (để đảm bảo luôn trả về thành công nếu transaction commit)
            $responseData = [
                'success' => true,
                'message' => 'Mua Source Code Thành Công!',
                'redirect' => route('download.index', ['mgd' => $mgd]) // URL để tải source code
            ];

            // Gửi thông báo Telegram cho admin (non-blocking - không chặn response)
            // Nếu Telegram lỗi, không làm fail transaction
            try {
                $this->telegramService->notifyNewOrder('sourcecode', [
                    'username' => $user->taikhoan, // Username người dùng
                    'mgd' => (string)$mgd, // Mã giao dịch
                    'product_name' => $sourceCode->name, // Tên source code
                    'time' => date('d/m/Y - H:i:s') // Thời gian định dạng Việt Nam
                ]);
            } catch (\Exception $e) {
                // Nếu Telegram lỗi, chỉ ghi log, không làm fail response
                Log::error('Telegram error for sourcecode order ' . $mgd . ': ' . $e->getMessage());
                // Không làm fail response - vì đơn hàng đã được tạo thành công
            }

            // Gửi email xác nhận đơn hàng cho người dùng (non-blocking - không chặn response)
            if ($user->email) {
                try {
                    // Ghi log khi bắt đầu gửi email
                    Log::info('Sending sourcecode order confirmation email', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'source_code_id' => $sourceCodeId
                    ]);
                    
                    // Gửi email xác nhận đơn hàng
                    Mail::to($user->email)->send(new OrderConfirmationMail(
                        $history, // Đối tượng SourceCodeHistory chứa thông tin đơn hàng
                        'sourcecode', // Loại đơn hàng: sourcecode
                        $user, // Đối tượng User
                        [
                            'price' => $price, // Giá source code
                            'source_code_name' => $sourceCode->name, // Tên source code
                        ]
                    ));
                    
                    // Ghi log khi gửi email thành công
                    Log::info('Sourcecode order confirmation email sent successfully', [
                        'user_email' => $user->email,
                        'mgd' => $mgd
                    ]);
                } catch (\Exception $e) {
                    // Nếu có lỗi khi gửi email, chỉ ghi log, không làm fail transaction
                    Log::error('Sourcecode order email error', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'error' => $e->getMessage(), // Thông báo lỗi
                        'trace' => $e->getTraceAsString() // Stack trace để debug
                    ]);
                    // Không throw exception, tiếp tục xử lý - vì đơn hàng đã được tạo thành công
                }
            } else {
                // Nếu user không có email, ghi log cảnh báo
                Log::warning('Sourcecode order - User has no email', [
                    'user_id' => $user->id,
                    'username' => $user->taikhoan,
                    'mgd' => $mgd
                ]);
            }

            // Luôn trả về response thành công nếu transaction đã commit
            // Dù Telegram hoặc Email có lỗi, đơn hàng vẫn được tạo thành công
            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            // Nếu có lỗi bất kỳ trong transaction, rollback
            DB::rollBack();
            
            // Ghi log chi tiết lỗi để debug
            Log::error('Checkout SourceCode Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Trả về JSON response lỗi
            return response()->json([
                'success' => false,
                'message' => 'Không Thể Mua Vào Lúc Này! Vui lòng thử lại sau.'
            ]);
        }
    }
}
