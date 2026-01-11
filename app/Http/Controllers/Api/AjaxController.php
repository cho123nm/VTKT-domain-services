<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Api
namespace App\Http\Controllers\Api;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model cần thiết
use App\Models\Domain; // Model quản lý thông tin domain
use App\Models\History; // Model lưu lịch sử mua domain
use App\Models\Hosting; // Model quản lý gói hosting
use App\Models\HostingHistory; // Model lưu lịch sử mua hosting
use App\Models\VPS; // Model quản lý gói VPS
use App\Models\VPSHistory; // Model lưu lịch sử mua VPS
use App\Models\SourceCode; // Model quản lý source code
use App\Models\SourceCodeHistory; // Model lưu lịch sử mua source code
use App\Models\Card; // Model quản lý thẻ cào
use App\Models\User; // Model quản lý người dùng
use App\Models\Settings; // Model quản lý cài đặt hệ thống
// Import các Service cần thiết
use App\Services\DomainService; // Service xử lý logic domain (WHOIS, etc.)
use App\Services\TelegramService; // Service gửi thông báo Telegram
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Log; // Facade để ghi log
use Illuminate\Support\Facades\DB; // Facade để thao tác database
use Illuminate\Support\Facades\Mail; // Facade để gửi email
use Illuminate\Support\Str; // Helper class để tạo chuỗi ngẫu nhiên
use Carbon\Carbon; // Library để xử lý ngày tháng

/**
 * Class AjaxController
 * Controller xử lý các AJAX request từ frontend
 * Bao gồm: kiểm tra domain, mua domain/hosting/VPS/source code, cập nhật DNS, nạp thẻ
 */
class AjaxController extends Controller
{
    // Thuộc tính lưu trữ instance của DomainService
    protected $domainService;
    // Thuộc tính lưu trữ instance của TelegramService
    protected $telegramService;

    /**
     * Hàm khởi tạo (Constructor)
     * Dependency Injection: Laravel tự động inject DomainService và TelegramService vào đây
     * 
     * @param DomainService $domainService - Service để xử lý logic domain
     * @param TelegramService $telegramService - Service để gửi thông báo Telegram
     */
    public function __construct(DomainService $domainService, TelegramService $telegramService)
    {
        // Gán DomainService vào thuộc tính của class
        $this->domainService = $domainService;
        // Gán TelegramService vào thuộc tính của class
        $this->telegramService = $telegramService;
    }
    
    /**
     * Kiểm tra domain có sẵn không (WHOIS check)
     * Kiểm tra domain đã được đăng ký chưa bằng nhiều phương pháp
     * 
     * @param Request $request - HTTP request chứa name (tên domain) và domain (đuôi miền)
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function checkDomain(Request $request)
    {
        // Lấy và chuẩn hóa tên domain từ request (chuyển thành chữ thường, loại bỏ khoảng trắng)
        $tenmien = strtolower(trim($request->input('name', ''))); // Tên domain (ví dụ: "example")
        $domainSuffix = $request->input('domain', ''); // Đuôi domain (ví dụ: ".com")
        // Tạo domain đầy đủ (ví dụ: "example.com")
        $ok = $tenmien . $domainSuffix;

        // Lấy danh sách đuôi miền hỗ trợ từ database
        // pluck('duoi') lấy tất cả giá trị cột 'duoi', map() chuyển thành chữ thường
        $supported = Domain::all()->pluck('duoi')->map(function($d) {
            return strtolower($d);
        })->toArray();

        // Validate: kiểm tra tên domain không được rỗng
        if ($tenmien === '') {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Tên Miền',
                'html' => '<script>toastr.error("Vui Lòng Nhập Tên Miền", "Thông Báo");</script>'
            ]);
        }

        // Validate: kiểm tra đuôi miền có trong danh sách hỗ trợ không
        if (!in_array(strtolower($domainSuffix), $supported, true)) {
            return response()->json([
                'success' => false,
                'message' => "Đuôi Miền {$domainSuffix} Không Hỗ Trợ!",
                'html' => '<script>toastr.error("Đuôi Miền '.$domainSuffix.' Không Hỗ Trợ! ", "Thông Báo");</script>'
            ]);
        }

        // Validate định dạng domain: chỉ chứa chữ, số, gạch ngang; không bắt đầu/kết thúc bằng gạch ngang
        $labelRegex = '/^(?!-)[a-z0-9-]{1,63}(?<!-)$/'; // Regex pattern cho từng phần của domain
        $labels = explode('.', $tenmien); // Tách domain thành các phần (ví dụ: "example.com" -> ["example", "com"])
        foreach ($labels as $label) {
            // Kiểm tra từng phần của domain
            if ($label === '' || !preg_match($labelRegex, $label)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên miền không hợp lệ (chỉ chữ, số, gạch ngang; không bắt đầu/kết thúc bằng -)',
                    'html' => '<script>toastr.error("Tên miền không hợp lệ (chỉ chữ, số, gạch ngang; không bắt đầu/kết thúc bằng -)", "Thông Báo");</script>'
                ]);
            }
        }
        // Kiểm tra độ dài domain không được quá 253 ký tự (RFC 1035)
        if (strlen($ok) > 253) {
            return response()->json([
                'success' => false,
                'message' => 'Tên miền quá dài',
                'html' => '<script>toastr.error("Tên miền quá dài", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra DNS A Records - xem domain có trỏ đến IP nào không
        $hasARecord = checkdnsrr($ok, 'A');

        // Kiểm tra Ping - xem domain có phản hồi ping không
        $pingResult = $this->checkDomainByPing($ok);

        // Kiểm tra WHOIS - xem domain đã được đăng ký chưa (cho domain Việt Nam)
        $whoisResult = $this->checkWhoisVietnam($ok);

        // Đếm số bằng chứng cho thấy domain đã được đăng ký
        $strongEvidence = 0;
        if ($hasARecord) $strongEvidence++; // Có DNS A record
        if ($pingResult === true) $strongEvidence++; // Ping thành công
        if ($whoisResult === true) $strongEvidence++; // WHOIS cho thấy đã đăng ký

        // Nếu có ít nhất 2 bằng chứng → domain đã được đăng ký
        if ($strongEvidence >= 2) {
            return response()->json([
                'success' => false,
                'message' => "Tên Miền {$ok} Đã Được Đăng Ký!",
                'html' => '<script>toastr.error("Tên Miền ' . $ok . ' Đã Được Đăng Ký!", "Thông Báo");</script>'
            ]);
        }

        // Nếu không có đủ bằng chứng → domain có thể đăng ký được
        // Tạo URL checkout để user có thể mua domain
        $checkoutUrl = route('domain.checkout', ['domain' => $ok]);
        // Tạo HTML để hiển thị thông báo thành công và link checkout
        $html = '<script>toastr.success("Bạn Có Thể Mua Miền ' . $ok . ' Ngay Bây Giờ", "Thông Báo");</script>';
        $html .= '<center><b class="text-danger">Bạn Có Thể Đăng Ký Tên Miền Này Ngay Bây Giờ <a href="' . $checkoutUrl . '" class="text-success">Tại Đây</a></b><br><br></center>';

        // Trả về JSON response thành công
        return response()->json([
            'success' => true,
            'message' => "Bạn Có Thể Mua Miền {$ok} Ngay Bây Giờ",
            'html' => $html
        ]);
    }

    /**
     * Kiểm tra domain bằng cách ping (resolve IP)
     * Private method - chỉ được gọi từ trong class này
     * 
     * @param string $domain - Domain cần kiểm tra (ví dụ: "example.com")
     * @return bool|null - True nếu domain có IP hợp lệ và thuộc các website lớn, False nếu không, null nếu không xác định được
     */
    private function checkDomainByPing($domain)
    {
        // Resolve domain thành IP address
        $ip = gethostbyname($domain);
        
        // Nếu IP trùng với domain (không resolve được), trả về false
        if ($ip === $domain) {
            return false;
        }
        
        // Danh sách IP cần loại trừ (DNS server, localhost, CDN, etc.)
        $excludeIPs = [
            '127.0.0.1', // Localhost
            '0.0.0.0', // Invalid IP
            '8.8.8.8', // Google DNS
            '1.1.1.1', // Cloudflare DNS
            '208.67.222.222', // OpenDNS
            '74.125.224.72', // Google IP
            '173.194.44.0', // Google IP range
            '216.58.192.0', // Google IP range
            '104.21.0.0', // Cloudflare IP range
            '172.67.0.0', // Cloudflare IP range
            '141.101.0.0', // Cloudflare IP range
            '162.158.0.0', // Cloudflare IP range
            '198.41.0.0', // Cloudflare IP range
            '188.114.0.0', // Cloudflare IP range
        ];
        
        // Kiểm tra IP có trong danh sách loại trừ không
        foreach ($excludeIPs as $excludeIP) {
            // So sánh toàn bộ IP hoặc 8 ký tự đầu (để match IP range)
            if ($ip === $excludeIP || strpos($ip, substr($excludeIP, 0, 8)) === 0) {
                return false; // IP bị loại trừ, không tính là domain đã đăng ký
            }
        }
        
        // Kiểm tra IP có phải là IP công cộng hợp lệ không (không phải private/reserved range)
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false; // IP không hợp lệ hoặc là private/reserved range
        }
        
        // Danh sách IP range của các website lớn (Google, Facebook, Microsoft, Cloudflare, etc.)
        $majorWebsiteIPs = [
            '142.250.0.0', // Google IP range
            '157.240.0.0', // Facebook IP range
            '31.13.0.0', // Facebook IP range
            '66.220.0.0', // Facebook IP range
            '69.63.0.0', // Facebook IP range
            '104.244.0.0', // Twitter IP range
            '151.101.0.0', // Reddit IP range
            '13.107.0.0', // Microsoft IP range
            '52.84.0.0', // Amazon AWS IP range
            '104.16.0.0', // Cloudflare IP range
            '172.64.0.0', // Cloudflare IP range
            '198.41.0.0', // Cloudflare IP range
        ];
        
        // Kiểm tra IP có thuộc các website lớn không
        foreach ($majorWebsiteIPs as $majorIP) {
            // So sánh 8 ký tự đầu để match IP range
            if (strpos($ip, substr($majorIP, 0, 8)) === 0) {
                return true; // Domain có IP thuộc website lớn → có thể đã đăng ký
            }
        }
        
        // Nếu không match với bất kỳ điều kiện nào, trả về false
        return false;
    }

    /**
     * Kiểm tra domain bằng WHOIS API của inet.vn (cho domain Việt Nam)
     * Private method - chỉ được gọi từ trong class này
     * 
     * @param string $domain - Domain cần kiểm tra (ví dụ: "example.com")
     * @return bool|null - True nếu domain đã đăng ký, False nếu chưa đăng ký, null nếu không xác định được
     */
    private function checkWhoisVietnam($domain)
    {
        // Tạo URL API WHOIS của inet.vn
        $url = "https://domain.inet.vn/api/whois?domain=" . urlencode($domain);
        
        // Khởi tạo cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Trả về response dưới dạng string
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Đặt timeout 5 giây
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Tắt verify SSL (để tránh lỗi certificate)
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'); // Set User-Agent
        
        // Thực thi request và lấy response
        $check = curl_exec($ch);
        // Lấy HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Đóng cURL
        curl_close($ch);
        
        // Nếu HTTP status code không phải 200 hoặc không có response, trả về null
        if ($httpCode != 200 || !$check) {
            return null;
        }
        
        // Chuyển response thành chữ thường để so sánh không phân biệt hoa thường
        $checkLower = strtolower($check);
        
        // Danh sách cụm từ cho thấy domain đã được đăng ký
        $strongRegisteredPhrases = [
            'registry expiry date:', // Ngày hết hạn đăng ký
            'expiration date:', // Ngày hết hạn
            'registration date:', // Ngày đăng ký
            'created:', // Ngày tạo
            'registrar:', // Nhà đăng ký
            'domain status: ok', // Trạng thái domain: OK
            'domain status: active', // Trạng thái domain: Active
        ];
        
        // Kiểm tra response có chứa cụm từ cho thấy domain đã đăng ký không
        foreach ($strongRegisteredPhrases as $phrase) {
            if (strpos($checkLower, $phrase) !== false) {
                return true; // Domain đã được đăng ký
            }
        }
        
        // Danh sách cụm từ cho thấy domain chưa được đăng ký
        $availablePhrases = [
            'no match', // Không tìm thấy
            'not found', // Không tìm thấy
            'no data found', // Không có dữ liệu
            'không tìm thấy', // Không tìm thấy (tiếng Việt)
            'chưa được đăng ký', // Chưa được đăng ký (tiếng Việt)
            'domain not found', // Domain không tìm thấy
            'no entries found' // Không có entry nào
        ];
        
        // Kiểm tra response có chứa cụm từ cho thấy domain chưa đăng ký không
        foreach ($availablePhrases as $phrase) {
            if (strpos($checkLower, $phrase) !== false) {
                return false; // Domain chưa được đăng ký
            }
        }
        
        // Nếu không match với bất kỳ cụm từ nào, trả về null (không xác định được)
        return null;
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
            History::where('mgd', $mgd)->exists() || // Kiểm tra trong bảng domain history
            HostingHistory::where('mgd', $mgd)->exists() || // Kiểm tra trong bảng hosting history
            VPSHistory::where('mgd', $mgd)->exists() || // Kiểm tra trong bảng VPS history
            SourceCodeHistory::where('mgd', $mgd)->exists() // Kiểm tra trong bảng source code history
        );
        // Ép kiểu về string và trả về
        return (string)$mgd;
    }

    /**
     * Mua domain (AJAX endpoint)
     * Xử lý mua domain từ AJAX request
     * 
     * @param Request $request - HTTP request chứa domain, ns1, ns2, hsd
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function buyDomain(Request $request)
    {
        // Lấy dữ liệu từ request
        $domain = $request->input('domain', ''); // Tên domain đầy đủ (ví dụ: "example.com")
        $ns1 = $request->input('ns1', ''); // Nameserver 1
        $ns2 = $request->input('ns2', ''); // Nameserver 2
        $hsd = $request->input('hsd', ''); // Hạn sử dụng (chỉ nhận '1')
        // Tạo mã giao dịch duy nhất
        $mgd = $this->generateMGD();

        // Validate: kiểm tra các trường không được rỗng
        if ($domain == "" || $ns1 == "" || $ns2 == "" || $hsd == "") {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Đầy Đủ Thông Tin',
                'html' => '<script>toastr.error("Vui Lòng Nhập Đầy Đủ Thông Tin", "Thông Báo");</script>'
            ]);
        }

        // Lấy đuôi miền từ domain đầy đủ
        $explode = explode(".", $domain); // Tách domain thành mảng (ví dụ: ["example", "com"])
        $duoimien = isset($explode[1]) ? '.' . $explode[1] : ''; // Lấy đuôi miền (ví dụ: ".com")

        // Tìm thông tin domain theo đuôi miền trong database
        $domainInfo = Domain::findByDuoi($duoimien);
        // Nếu không tìm thấy, trả về lỗi
        if (!$domainInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin đuôi miền!',
                'html' => '<script>toastr.error("Không tìm thấy thông tin đuôi miền!", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra domain đã được mua chưa (trong bảng History)
        $checkls = History::where('domain', $domain)->first();

        // Lấy giá domain từ thông tin domain
        $tienphaitra = $domainInfo->price;

        // Validate: kiểm tra giá hợp lệ (phải > 0)
        if ($tienphaitra <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Giá tiền không hợp lệ!',
                'html' => '<script>toastr.error("Giá tiền không hợp lệ!", "Thông Báo");</script>'
            ]);
        }

        // Chỉ xử lý nếu hsd = '1' (hạn sử dụng 1 năm)
        if ($hsd == '1') {
            // Kiểm tra user đã đăng nhập chưa - sử dụng $request->session()
            if (!$request->hasSession() || !$request->session()->has('users')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!',
                    'html' => '<script>toastr.error("Vui Lòng Đăng Nhập Để Thực Hiện!", "Thông Báo");</script>'
                ]);
            }

            // Tìm user trong database theo username trong session
            $user = User::findByUsername($request->session()->get('users'));
            // Nếu không tìm thấy user, trả về lỗi
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin tài khoản!',
                    'html' => '<script>toastr.error("Không tìm thấy thông tin tài khoản!", "Thông Báo");</script>'
                ]);
            }

            // Kiểm tra domain đã tồn tại chưa (đã được mua chưa)
            if ($checkls) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn Hàng Này Đã Thanh Toán, Chờ Xử Lí!',
                    'html' => '<script>toastr.error("Đơn Hàng Này Đã Thanh Toán, Chờ Xử Lí!", "Thông Báo");</script>'
                ]);
            }

            // Kiểm tra số dư tài khoản có đủ để thanh toán không
            if ($user->tien >= $tienphaitra) {
                // Bắt đầu transaction database
                DB::beginTransaction();
                try {
                    // Tạo chuỗi thời gian
                    $time = date('Y-m-d H:i:s');
                    
                    // Tạo đơn hàng domain mới
                    $history = new History();
                    $history->uid = $user->id; // ID người dùng
                    $history->domain = $domain; // Tên domain đầy đủ
                    $history->ns1 = $ns1; // Nameserver 1
                    $history->ns2 = $ns2; // Nameserver 2
                    $history->hsd = (int)$hsd; // Hạn sử dụng (ép kiểu về int)
                    $history->status = 0; // Trạng thái: 0 = Chờ xử lý
                    $history->mgd = $mgd; // Mã giao dịch
                    $history->time = $time; // Thời gian tạo đơn hàng
                    $history->timedns = '0'; // Thời gian DNS (mặc định: '0')
                    
                    // Nếu lưu đơn hàng thành công
                    if ($history->save()) {
                        // Trừ số dư tài khoản người dùng
                        $user->incrementBalance(-1 * (int)$tienphaitra);
                        // Commit transaction
                        DB::commit();
                        
                        // Gửi thông báo Telegram cho admin về đơn hàng mới
                        try {
                            $this->telegramService->notifyNewOrder('domain', [
                                'username' => $user->taikhoan, // Username người mua
                                'mgd' => (string)$mgd, // Mã giao dịch
                                'domain' => $domain, // Tên domain
                                'ns1' => $ns1, // Nameserver 1
                                'ns2' => $ns2, // Nameserver 2
                                'time' => date('d/m/Y - H:i:s') // Thời gian (định dạng Việt Nam)
                            ]);
                        } catch (\Exception $e) {
                            // Ghi log lỗi nếu không gửi được Telegram (không làm gián đoạn quá trình)
                            Log::error('Telegram error for domain order ' . $mgd . ': ' . $e->getMessage());
                        }
                        
                        // Gửi email xác nhận đơn hàng cho user
                        if ($user->email) {
                            try {
                                // Ghi log trước khi gửi email
                                Log::info('Sending domain order confirmation email (AjaxController)', [
                                    'user_email' => $user->email, // Email người nhận
                                    'mgd' => $mgd, // Mã giao dịch
                                    'domain' => $domain // Tên domain
                                ]);
                                
                                // Gửi email xác nhận đơn hàng
                                Mail::to($user->email)->send(new \App\Mail\OrderConfirmationMail(
                                    $history, // Đơn hàng
                                    'domain', // Loại đơn hàng
                                    $user, // User
                                    [
                                        'price' => $tienphaitra, // Giá tiền
                                        'domain' => $domain, // Tên domain
                                        'ns1' => $ns1, // Nameserver 1
                                        'ns2' => $ns2, // Nameserver 2
                                    ]
                                ));
                                
                                // Ghi log sau khi gửi email thành công
                                Log::info('Domain order confirmation email sent successfully (AjaxController)', [
                                    'user_email' => $user->email, // Email người nhận
                                    'mgd' => $mgd // Mã giao dịch
                                ]);
                            } catch (\Exception $e) {
                                // Ghi log lỗi nếu không gửi được email (không làm gián đoạn quá trình)
                                Log::error('Domain order email error (AjaxController)', [
                                    'user_email' => $user->email, // Email người nhận
                                    'mgd' => $mgd, // Mã giao dịch
                                    'error' => $e->getMessage(), // Thông báo lỗi
                                    'trace' => $e->getTraceAsString() // Stack trace
                                ]);
                            }
                        } else {
                            // Ghi log cảnh báo nếu user không có email
                            Log::warning('Domain order - User has no email (AjaxController)', [
                                'user_id' => $user->id, // ID user
                                'username' => $user->taikhoan, // Username
                                'mgd' => $mgd // Mã giao dịch
                            ]);
                        }
                        
                        // Trả về JSON response thành công
                        return response()->json([
                            'success' => true,
                            'message' => 'Mua Tên Miền Thành Công, Chờ Xử Lí!',
                            'html' => '<script>toastr.success("Mua Tên Miền Thành Công, Chờ Xử Lí!", "Thông Báo");</script>'
                        ]);
                    } else {
                        // Nếu lưu thất bại, rollback transaction
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Không Thể Mua Vào Lúc Này!',
                            'html' => '<script>toastr.error("Không Thể Mua Vào Lúc Này!", "Thông Báo");</script>'
                        ]);
                    }
                } catch (\Exception $e) {
                    // Nếu có lỗi, rollback transaction và ghi log
                    DB::rollBack();
                    Log::error('Error buying domain: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Có lỗi xảy ra, vui lòng thử lại!',
                        'html' => '<script>toastr.error("Có lỗi xảy ra, vui lòng thử lại!", "Thông Báo");</script>'
                    ]);
                }
            } else {
                // Nếu số dư không đủ, trả về lỗi
                return response()->json([
                    'success' => false,
                    'message' => 'Số Dư Tài Khoản Không Đủ!',
                    'html' => '<script>toastr.error("Số Dư Tài Khoản Không Đủ!", "Thông Báo");</script>'
                ]);
            }
        } else {
            // Nếu hạn sử dụng không hợp lệ (không phải '1'), trả về lỗi
            return response()->json([
                'success' => false,
                'message' => 'Hạn Sử Dụng Không Hợp Lệ!',
                'html' => '<script>toastr.error("Hạn Sử Dụng Không Hợp Lệ!", "Thông Báo");</script>'
            ]);
        }
    }

    /**
     * Mua hosting (AJAX endpoint)
     * Xử lý mua hosting từ AJAX request
     * 
     * @param Request $request - HTTP request chứa hosting_id và period
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function buyHosting(Request $request)
    {
        // Lấy dữ liệu từ request
        $hostingId = $request->input('hosting_id', 0); // ID gói hosting
        $period = $request->input('period', ''); // Thời hạn: 'month' hoặc 'year'
        // Tạo mã giao dịch duy nhất
        $mgd = $this->generateMGD();

        // Validate: kiểm tra dữ liệu đầu vào không được rỗng
        if ($hostingId == 0 || $period == "") {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Đầy Đủ Thông Tin!',
                'html' => '<script>toastr.error("Vui Lòng Nhập Đầy Đủ Thông Tin!", "Thông Báo");</script>'
            ]);
        }

        // Validate: kiểm tra period chỉ nhận 'month' hoặc 'year'
        if (!in_array($period, ['month', 'year'])) {
            return response()->json([
                'success' => false,
                'message' => 'Thời gian thuê không hợp lệ!',
                'html' => '<script>toastr.error("Thời gian thuê không hợp lệ!", "Thông Báo");</script>'
            ]);
        }

        // Tìm gói hosting trong database theo ID
        $hosting = Hosting::find($hostingId);
        // Nếu không tìm thấy, trả về lỗi
        if (!$hosting) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy gói hosting!',
                'html' => '<script>toastr.error("Không tìm thấy gói hosting!", "Thông Báo");</script>'
            ]);
        }

        // Xác định giá dựa trên thời hạn: tháng hoặc năm
        $tienphaitra = $period === 'month' ? $hosting->price_month : $hosting->price_year;

        // Validate: kiểm tra giá hợp lệ (phải > 0)
        if ($tienphaitra <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Giá tiền không hợp lệ!',
                'html' => '<script>toastr.error("Giá tiền không hợp lệ!", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra user đã đăng nhập chưa
        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!',
                'html' => '<script>toastr.error("Vui Lòng Đăng Nhập Để Thực Hiện!", "Thông Báo");</script>'
            ]);
        }

        // Tìm user trong database theo username trong session
        $user = User::findByUsername($request->session()->get('users'));
        // Nếu không tìm thấy user, trả về lỗi
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!',
                'html' => '<script>toastr.error("Không tìm thấy thông tin tài khoản!", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra số dư tài khoản có đủ để thanh toán không
        if ($user->tien >= $tienphaitra) {
            // Bắt đầu transaction database
            DB::beginTransaction();
            try {
                // Tạo chuỗi thời gian
                $time = date('Y-m-d H:i:s');
                
                // Tạo đơn hàng hosting mới
                $history = new HostingHistory();
                $history->uid = $user->id; // ID người dùng
                $history->hosting_id = $hostingId; // ID gói hosting
                $history->period = $period; // Thời hạn: 'month' hoặc 'year'
                $history->mgd = $mgd; // Mã giao dịch
                $history->status = 1; // Trạng thái: 1 = Đã duyệt ngay
                $history->time = $time; // Thời gian tạo đơn hàng
                
                // Nếu lưu đơn hàng thành công
                if ($history->save()) {
                    // Trừ số dư tài khoản người dùng
                    $user->incrementBalance(-1 * (int)$tienphaitra);
                    // Commit transaction
                    DB::commit();
                    
                    // Tạo URL liên hệ admin
                    $contactUrl = route('contact-admin', ['type' => 'hosting', 'mgd' => $mgd]);
                    // Trả về JSON response thành công với script redirect
                    return response()->json([
                        'success' => true,
                        'message' => 'Mua Hosting Thành Công!',
                        'html' => '<script>toastr.success("Mua Hosting Thành Công!", "Thông Báo");</script><script>setTimeout(function(){ window.location.href = "' . $contactUrl . '"; }, 1500);</script>',
                        'redirect' => $contactUrl
                    ]);
                } else {
                    // Nếu lưu thất bại, rollback transaction
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Không Thể Mua Vào Lúc Này!',
                        'html' => '<script>toastr.error("Không Thể Mua Vào Lúc Này!", "Thông Báo");</script>'
                    ]);
                }
            } catch (\Exception $e) {
                // Nếu có lỗi, rollback transaction và ghi log
                DB::rollBack();
                Log::error('Error buying hosting: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại!',
                    'html' => '<script>toastr.error("Có lỗi xảy ra, vui lòng thử lại!", "Thông Báo");</script>'
                ]);
            }
        } else {
            // Nếu số dư không đủ, trả về lỗi
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!',
                'html' => '<script>toastr.error("Số Dư Tài Khoản Không Đủ!", "Thông Báo");</script>'
            ]);
        }
    }

    /**
     * Mua VPS (AJAX endpoint)
     * Xử lý mua VPS từ AJAX request
     * 
     * @param Request $request - HTTP request chứa vps_id và period
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function buyVPS(Request $request)
    {
        // Lấy dữ liệu từ request
        $vpsId = $request->input('vps_id', 0); // ID gói VPS
        $period = $request->input('period', ''); // Thời hạn: 'month' hoặc 'year'
        // Tạo mã giao dịch duy nhất
        $mgd = $this->generateMGD();

        // Validate: kiểm tra dữ liệu đầu vào không được rỗng
        if ($vpsId == 0 || $period == "") {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Đầy Đủ Thông Tin!',
                'html' => '<script>toastr.error("Vui Lòng Nhập Đầy Đủ Thông Tin!", "Thông Báo");</script>'
            ]);
        }

        // Validate: kiểm tra period chỉ nhận 'month' hoặc 'year'
        if (!in_array($period, ['month', 'year'])) {
            return response()->json([
                'success' => false,
                'message' => 'Thời gian thuê không hợp lệ!',
                'html' => '<script>toastr.error("Thời gian thuê không hợp lệ!", "Thông Báo");</script>'
            ]);
        }

        // Tìm gói VPS trong database theo ID
        $vps = VPS::find($vpsId);
        // Nếu không tìm thấy, trả về lỗi
        if (!$vps) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy gói VPS!',
                'html' => '<script>toastr.error("Không tìm thấy gói VPS!", "Thông Báo");</script>'
            ]);
        }

        // Xác định giá dựa trên thời hạn: tháng hoặc năm
        $tienphaitra = $period === 'month' ? $vps->price_month : $vps->price_year;

        // Validate: kiểm tra giá hợp lệ (phải > 0)
        if ($tienphaitra <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Giá tiền không hợp lệ!',
                'html' => '<script>toastr.error("Giá tiền không hợp lệ!", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra user đã đăng nhập chưa
        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!',
                'html' => '<script>toastr.error("Vui Lòng Đăng Nhập Để Thực Hiện!", "Thông Báo");</script>'
            ]);
        }

        // Tìm user trong database theo username trong session
        $user = User::findByUsername($request->session()->get('users'));
        // Nếu không tìm thấy user, trả về lỗi
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!',
                'html' => '<script>toastr.error("Không tìm thấy thông tin tài khoản!", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra số dư tài khoản có đủ để thanh toán không
        if ($user->tien >= $tienphaitra) {
            // Bắt đầu transaction database
            DB::beginTransaction();
            try {
                // Tạo chuỗi thời gian
                $time = date('Y-m-d H:i:s');
                
                // Tạo đơn hàng VPS mới
                $history = new VPSHistory();
                $history->uid = $user->id; // ID người dùng
                $history->vps_id = $vpsId; // ID gói VPS
                $history->period = $period; // Thời hạn: 'month' hoặc 'year'
                $history->mgd = $mgd; // Mã giao dịch
                $history->status = 1; // Trạng thái: 1 = Đã duyệt ngay
                $history->time = $time; // Thời gian tạo đơn hàng
                
                // Nếu lưu đơn hàng thành công
                if ($history->save()) {
                    // Trừ số dư tài khoản người dùng
                    $user->incrementBalance(-1 * (int)$tienphaitra);
                    // Commit transaction
                    DB::commit();
                    
                    // Tạo URL liên hệ admin
                    $contactUrl = route('contact-admin', ['type' => 'vps', 'mgd' => $mgd]);
                    // Trả về JSON response thành công với script redirect
                    return response()->json([
                        'success' => true,
                        'message' => 'Mua VPS Thành Công!',
                        'html' => '<script>toastr.success("Mua VPS Thành Công!", "Thông Báo");</script><script>setTimeout(function(){ window.location.href = "' . $contactUrl . '"; }, 1500);</script>',
                        'redirect' => $contactUrl
                    ]);
                } else {
                    // Nếu lưu thất bại, rollback transaction
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Không Thể Mua Vào Lúc Này!',
                        'html' => '<script>toastr.error("Không Thể Mua Vào Lúc Này!", "Thông Báo");</script>'
                    ]);
                }
            } catch (\Exception $e) {
                // Nếu có lỗi, rollback transaction và ghi log
                DB::rollBack();
                Log::error('Error buying VPS: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại!',
                    'html' => '<script>toastr.error("Có lỗi xảy ra, vui lòng thử lại!", "Thông Báo");</script>'
                ]);
            }
        } else {
            // Nếu số dư không đủ, trả về lỗi
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!',
                'html' => '<script>toastr.error("Số Dư Tài Khoản Không Đủ!", "Thông Báo");</script>'
            ]);
        }
    }

    /**
     * Mua source code (AJAX endpoint)
     * Xử lý mua source code từ AJAX request
     * 
     * @param Request $request - HTTP request chứa source_code_id
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function buySourceCode(Request $request)
    {
        // Lấy dữ liệu từ request
        $sourceCodeId = $request->input('source_code_id', 0); // ID source code
        // Tạo mã giao dịch duy nhất
        $mgd = $this->generateMGD();

        // Validate: kiểm tra source_code_id không được = 0
        if ($sourceCodeId == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Chọn Source Code!',
                'html' => '<script>toastr.error("Vui Lòng Chọn Source Code!", "Thông Báo");</script>'
            ]);
        }

        // Tìm source code trong database theo ID
        $sourceCode = SourceCode::find($sourceCodeId);
        // Nếu không tìm thấy, trả về lỗi
        if (!$sourceCode) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy source code!',
                'html' => '<script>toastr.error("Không tìm thấy source code!", "Thông Báo");</script>'
            ]);
        }

        // Lấy giá source code
        $tienphaitra = $sourceCode->price;

        // Validate: kiểm tra giá hợp lệ (phải > 0)
        if ($tienphaitra <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Giá tiền không hợp lệ!',
                'html' => '<script>toastr.error("Giá tiền không hợp lệ!", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra user đã đăng nhập chưa
        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!',
                'html' => '<script>toastr.error("Vui Lòng Đăng Nhập Để Thực Hiện!", "Thông Báo");</script>'
            ]);
        }

        // Tìm user trong database theo username trong session
        $user = User::findByUsername($request->session()->get('users'));
        // Nếu không tìm thấy user, trả về lỗi
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!',
                'html' => '<script>toastr.error("Không tìm thấy thông tin tài khoản!", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra số dư tài khoản có đủ để thanh toán không
        if ($user->tien >= $tienphaitra) {
            // Bắt đầu transaction database
            DB::beginTransaction();
            try {
                // Tạo chuỗi thời gian
                $time = date('Y-m-d H:i:s');
                
                // Tạo đơn hàng source code mới
                $history = new SourceCodeHistory();
                $history->uid = $user->id; // ID người dùng
                $history->source_code_id = $sourceCodeId; // ID source code
                $history->mgd = $mgd; // Mã giao dịch
                $history->status = 1; // Trạng thái: 1 = Đã duyệt ngay
                $history->time = $time; // Thời gian tạo đơn hàng
                
                // Nếu lưu đơn hàng thành công
                if ($history->save()) {
                    // Trừ số dư tài khoản người dùng
                    $user->incrementBalance(-1 * (int)$tienphaitra);
                    // Commit transaction
                    DB::commit();
                    
                    // Tạo URL trang download
                    $downloadUrl = route('download.index', ['mgd' => $mgd]);
                    // Trả về JSON response thành công với script redirect
                    return response()->json([
                        'success' => true,
                        'message' => 'Mua Source Code Thành Công!',
                        'html' => '<script>toastr.success("Mua Source Code Thành Công!", "Thông Báo");</script><script>setTimeout(function(){ window.location.href = "' . $downloadUrl . '"; }, 1500);</script>',
                        'redirect' => $downloadUrl
                    ]);
                } else {
                    // Nếu lưu thất bại, rollback transaction
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Không Thể Mua Vào Lúc Này!',
                        'html' => '<script>toastr.error("Không Thể Mua Vào Lúc Này!", "Thông Báo");</script>'
                    ]);
                }
            } catch (\Exception $e) {
                // Nếu có lỗi, rollback transaction và ghi log
                DB::rollBack();
                Log::error('Error buying source code: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại!',
                    'html' => '<script>toastr.error("Có lỗi xảy ra, vui lòng thử lại!", "Thông Báo");</script>'
                ]);
            }
        } else {
            // Nếu số dư không đủ, trả về lỗi
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!',
                'html' => '<script>toastr.error("Số Dư Tài Khoản Không Đủ!", "Thông Báo");</script>'
            ]);
        }
    }

    /**
     * Cập nhật DNS cho domain (AJAX endpoint)
     * Cho phép user cập nhật nameserver cho domain đã mua (có giới hạn chu kỳ 15 ngày)
     * 
     * @param Request $request - HTTP request chứa ns1, ns2, mgd
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function updateDns(Request $request)
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập!',
                'html' => '<script>toastr.error("Vui lòng đăng nhập!", "Thông Báo");</script>'
            ]);
        }

        // Lấy dữ liệu từ request
        $ns1 = $request->input('ns1'); // Nameserver 1 mới
        $ns2 = $request->input('ns2'); // Nameserver 2 mới
        $mgd = $request->input('mgd'); // Mã giao dịch của domain

        // Tìm user trong database theo username trong session
        $user = User::findByUsername($request->session()->get('users'));
        // Nếu không tìm thấy user, trả về lỗi
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng!',
                'html' => '<script>toastr.error("Không tìm thấy thông tin người dùng!", "Thông Báo");</script>'
            ]);
        }

        // Tìm đơn hàng domain theo mã giao dịch và ID user (đảm bảo user chỉ cập nhật DNS domain của mình)
        $checkmgd = History::where('uid', $user->id)
            ->where('mgd', $mgd)
            ->first();

        // Nếu không tìm thấy đơn hàng, trả về lỗi
        if (!$checkmgd) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền quản lý tên miền này!',
                'html' => '<script>toastr.error("Bạn không có quyền quản lý tên miền này!", "Thông Báo");</script>'
            ]);
        }

        // Validate: kiểm tra nameserver không được rỗng
        if ($ns1 == "" || $ns2 == "") {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Đầy Đủ Thông Tin!',
                'html' => '<script>toastr.error("Vui Lòng Nhập Đầy Đủ Thông Tin!", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra chu kỳ cập nhật DNS (timedns = '0' nghĩa là chưa cập nhật lần nào hoặc đã hết chu kỳ)
        if ($checkmgd->timedns == '0') {
            // Tính toán chu kỳ tiếp theo (15 ngày sau ngày hiện tại)
            $today = date('d/m/Y'); // Ngày hiện tại (ví dụ: "15/01/2024")
            $ex = explode("/", $today); // Tách thành mảng ["15", "01", "2024"]
            // Tính ngày sau 15 ngày (ví dụ: "30/01/2024")
            $chuky = ($ex[0] + 15) . '/' . $ex[1] . '/' . $ex[2];

            // Cập nhật nameserver mới
            $checkmgd->ns1 = $ns1; // Cập nhật NS1
            $checkmgd->ns2 = $ns2; // Cập nhật NS2
            $checkmgd->timedns = $chuky; // Lưu chu kỳ tiếp theo (15 ngày sau)
            $checkmgd->save(); // Lưu vào database

            // Trả về JSON response thành công
            return response()->json([
                'success' => true,
                'message' => 'Thay Đổi DNS Thành Công, Vui Lòng Chờ 12h - 24h Để DNS Mới Hoạt Động',
                'html' => '<script>toastr.success("Thay Đổi DNS Thành Công, Vui Lòng Chờ 12h - 24h Để DNS Mới Hoạt Động", "Thông Báo");</script>'
            ]);
        } else {
            // Nếu chưa hết chu kỳ 15 ngày, trả về lỗi
            return response()->json([
                'success' => false,
                'message' => 'Bạn Không Thể Cập Nhật Thông Tin Ngay Bây Giờ Vui Lòng Đợi Chu Kỳ 15 Qua!',
                'html' => '<script>toastr.error("Bạn Không Thể Cập Nhật Thông Tin Ngay Bây Giờ Vui Lòng Đợi Chu Kỳ 15 Qua!", "Thông Báo");</script>'
            ]);
        }
    }

    /**
     * Nạp thẻ cào (AJAX endpoint)
     * Xử lý nạp thẻ cào thông qua API cardvip.vn
     * 
     * @param Request $request - HTTP request chứa pin, serial, amount, type
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function rechargeCard(Request $request)
    {
        // Lấy và trim dữ liệu từ request
        $pin = trim($request->input('pin', '')); // Mã PIN thẻ cào
        $serial = trim($request->input('serial', '')); // Serial thẻ cào
        $amount = trim($request->input('amount', '')); // Mệnh giá thẻ
        $type = trim($request->input('type', '')); // Loại thẻ
        // Tạo request ID duy nhất: timestamp + số ngẫu nhiên từ 500000-999999
        $requestid = (string)time() . rand(500000, 999999);

        // Tạo chuỗi thời gian
        $time = date('Y-m-d H:i:s'); // Thời gian đầy đủ (ví dụ: "2024-01-15 10:30:45")
        $time2 = date('Y-m-d'); // Ngày (ví dụ: "2024-01-15")

        // Lấy cấu hình từ database
        $settings = Settings::getOne();
        $apikey = $settings->apikey ?? ''; // API key từ cài đặt (mặc định: chuỗi rỗng)
        $callback = $settings->callback ?? (config('app.url') . '/callback'); // Callback URL (mặc định: app URL + /callback)

        // Xác định user hiện tại từ session
        $user_id = 0; // Mặc định: 0 (chưa đăng nhập)
        if ($request->hasSession() && $request->session()->has('users')) {
            // Tìm user trong database theo username trong session
            $user = User::findByUsername($request->session()->get('users'));
            $user_id = $user ? $user->id : 0; // Lấy ID user nếu tìm thấy, không thì 0
        }

        // Validate cơ bản: danh sách loại thẻ được hỗ trợ
        $allowedTypes = ['VIETTEL', 'VINAPHONE', 'MOBIFONE', 'GATE', 'ZING', 'VNMOBI', 'VIETNAMMOBILE'];
        
        // Validate: kiểm tra các trường không được rỗng
        if ($pin === "" || $serial === "" || $amount === "" || $type === "") {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Đầy Đủ Thông Tin!',
                'html' => '<script>toastr.error("Vui Lòng Nhập Đầy Đủ Thông Tin!", "Thông Báo");</script>'
            ]);
        }

        // Validate: kiểm tra mệnh giá chỉ chứa số
        if (!ctype_digit($amount)) {
            return response()->json([
                'success' => false,
                'message' => 'Mệnh giá không hợp lệ',
                'html' => '<script>toastr.error("Mệnh giá không hợp lệ", "Thông Báo");</script>'
            ]);
        }

        // Validate: kiểm tra loại thẻ có trong danh sách hỗ trợ không
        if (!in_array(strtoupper($type), $allowedTypes, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Loại thẻ không hỗ trợ',
                'html' => '<script>toastr.error("Loại thẻ không hỗ trợ", "Thông Báo");</script>'
            ]);
        }

        // Validate: kiểm tra user đã đăng nhập chưa
        if ($user_id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập lại để nạp thẻ',
                'html' => '<script>toastr.error("Vui lòng đăng nhập lại để nạp thẻ", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra thẻ đã tồn tại trong hệ thống chưa (theo PIN và Serial)
        $existingCard = Card::where('pin', $pin)
            ->where('serial', $serial)
            ->first();

        // Nếu thẻ đã tồn tại, trả về lỗi
        if ($existingCard) {
            return response()->json([
                'success' => false,
                'message' => 'Thẻ Đã Tồn Tại Trong Hệ Thống!',
                'html' => '<script>toastr.error("Thẻ Đã Tồn Tại Trong Hệ Thống!");</script>'
            ]);
        }

        // Chuẩn bị dữ liệu để gửi đến cardvip API
        $dataPost = [
            'APIKey' => $apikey, // API key từ cài đặt
            'NetworkCode' => $type, // Loại thẻ
            'PricesExchange' => $amount, // Mệnh giá
            'NumberCard' => $pin, // Mã PIN thẻ
            'SeriCard' => $serial, // Serial thẻ
            'IsFast' => true, // Nạp nhanh
            'RequestId' => $requestid, // Request ID duy nhất
            'UrlCallback' => $callback // Callback URL để nhận kết quả
        ];

        // Khởi tạo cURL để gửi request đến cardvip API
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://partner.cardvip.vn/api/createExchange", // URL API cardvip
            CURLOPT_RETURNTRANSFER => true, // Trả về response dưới dạng string
            CURLOPT_ENCODING => "", // Encoding (rỗng = tự động)
            CURLOPT_MAXREDIRS => 10, // Số lần redirect tối đa
            CURLOPT_TIMEOUT => 0, // Timeout (0 = không giới hạn)
            CURLOPT_FOLLOWLOCATION => true, // Theo dõi redirect
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // HTTP version 1.1
            CURLOPT_CUSTOMREQUEST => "POST", // Phương thức POST
            CURLOPT_POSTFIELDS => json_encode($dataPost), // Dữ liệu POST (JSON)
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"], // Header: JSON
        ]);

        // Thực thi request và lấy response
        $response = curl_exec($curl);
        // Lấy lỗi cURL (nếu có)
        $curlErr = curl_error($curl);
        // Lấy HTTP status code
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // Đóng cURL
        curl_close($curl);

        // Nếu request thất bại (response = false)
        if ($response === false) {
            // Lấy thông báo lỗi từ cURL hoặc thông báo mặc định
            $msg = $curlErr !== '' ? $curlErr : 'Không thể kết nối cổng nạp thẻ';
            return response()->json([
                'success' => false,
                'message' => $msg,
                'html' => '<script>toastr.error("' . $msg . '", "Thông Báo");</script>'
            ]);
        }

        // Decode JSON response thành mảng PHP
        $obj = json_decode($response, true);
        // Nếu không phải mảng hợp lệ, trả về lỗi
        if (!is_array($obj)) {
            return response()->json([
                'success' => false,
                'message' => 'API trả về dữ liệu không hợp lệ (HTTP ' . $httpCode . ')',
                'html' => '<script>toastr.error("API trả về dữ liệu không hợp lệ (HTTP ' . $httpCode . ')", "Thông Báo");</script>'
            ]);
        }

        // Lấy status và message từ response
        $status = $obj['status'] ?? null; // Status code từ API
        $message = $obj['message'] ?? ''; // Thông báo từ API

        // Xử lý theo status code từ API
        if ($status === 200) {
            // Nếu status = 200: thành công, lưu thẻ vào database
            $card = new Card();
            $card->uid = $user_id; // ID người dùng
            $card->pin = $pin; // Mã PIN thẻ
            $card->serial = $serial; // Serial thẻ
            $card->type = strtoupper($type); // Loại thẻ (chữ hoa)
            $card->amount = (string)$amount; // Mệnh giá (ép kiểu về string)
            $card->requestid = (string)$requestid; // Request ID (ép kiểu về string)
            $card->status = 0; // Trạng thái: 0 = Đang chờ duyệt
            $card->time = $time; // Thời gian tạo
            $card->time2 = $time2; // Ngày tạo
            $card->save(); // Lưu vào database

            // Trả về JSON response thành công
            return response()->json([
                'success' => true,
                'message' => 'Nạp thẻ thành công, vui lòng chờ 30s - 1 phút để duyệt',
                'html' => '<script>toastr.success("Nạp thẻ thành công, vui lòng chờ 30s - 1 phút để duyệt", "Thông Báo");</script>'
            ]);
        } elseif ($status === 400) {
            // Nếu status = 400: Thẻ đã tồn tại hoặc không hợp lệ
            return response()->json([
                'success' => false,
                'message' => 'Thẻ đã tồn tại hoặc không hợp lệ: ' . htmlspecialchars($message), // htmlspecialchars để tránh XSS
                'html' => '<script>toastr.error("Thẻ đã tồn tại hoặc không hợp lệ: ' . htmlspecialchars($message) . '", "Thông Báo");</script>'
            ]);
        } elseif ($status === 401) {
            // Nếu status = 401: Sai định dạng thẻ
            return response()->json([
                'success' => false,
                'message' => 'Sai định dạng thẻ: ' . htmlspecialchars($message),
                'html' => '<script>toastr.error("Sai định dạng thẻ: ' . htmlspecialchars($message) . '", "Thông Báo");</script>'
            ]);
        } elseif ($status === 403) {
            // Nếu status = 403: APIKey không hợp lệ hoặc bị hạn chế
            return response()->json([
                'success' => false,
                'message' => 'APIKey không hợp lệ hoặc bị hạn chế',
                'html' => '<script>toastr.error("APIKey không hợp lệ hoặc bị hạn chế", "Thông Báo");</script>'
            ]);
        } else {
            // Nếu status khác: Lỗi khác
            $safeMsg = $message !== '' ? htmlspecialchars($message) : 'Có lỗi khi gửi thẻ (HTTP ' . $httpCode . ')';
            return response()->json([
                'success' => false,
                'message' => $safeMsg,
                'html' => '<script>toastr.error("' . $safeMsg . '", "Thông Báo");</script>'
            ]);
        }
    }
}
