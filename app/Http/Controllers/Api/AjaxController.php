<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\History;
use App\Models\Hosting;
use App\Models\HostingHistory;
use App\Models\VPS;
use App\Models\VPSHistory;
use App\Models\SourceCode;
use App\Models\SourceCodeHistory;
use App\Models\Card;
use App\Models\User;
use App\Models\Settings;
use App\Services\DomainService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AjaxController extends Controller
{
    protected $domainService;
    protected $telegramService;

    public function __construct(DomainService $domainService, TelegramService $telegramService)
    {
        $this->domainService = $domainService;
        $this->telegramService = $telegramService;
    }
    /**
     * Kiểm tra domain có sẵn không
     */
    public function checkDomain(Request $request)
    {
        $tenmien = strtolower(trim($request->input('name', '')));
        $domainSuffix = $request->input('domain', '');
        $ok = $tenmien . $domainSuffix;

        // Lấy danh sách đuôi miền hỗ trợ
        $supported = Domain::all()->pluck('duoi')->map(function($d) {
            return strtolower($d);
        })->toArray();

        // Validate rỗng
        if ($tenmien === '') {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Tên Miền',
                'html' => '<script>toastr.error("Vui Lòng Nhập Tên Miền", "Thông Báo");</script>'
            ]);
        }

        // Validate đuôi miền
        if (!in_array(strtolower($domainSuffix), $supported, true)) {
            return response()->json([
                'success' => false,
                'message' => "Đuôi Miền {$domainSuffix} Không Hỗ Trợ!",
                'html' => '<script>toastr.error("Đuôi Miền '.$domainSuffix.' Không Hỗ Trợ! ", "Thông Báo");</script>'
            ]);
        }

        // Validate định dạng
        $labelRegex = '/^(?!-)[a-z0-9-]{1,63}(?<!-)$/';
        $labels = explode('.', $tenmien);
        foreach ($labels as $label) {
            if ($label === '' || !preg_match($labelRegex, $label)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên miền không hợp lệ (chỉ chữ, số, gạch ngang; không bắt đầu/kết thúc bằng -)',
                    'html' => '<script>toastr.error("Tên miền không hợp lệ (chỉ chữ, số, gạch ngang; không bắt đầu/kết thúc bằng -)", "Thông Báo");</script>'
                ]);
            }
        }
        if (strlen($ok) > 253) {
            return response()->json([
                'success' => false,
                'message' => 'Tên miền quá dài',
                'html' => '<script>toastr.error("Tên miền quá dài", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra DNS A Records
        $hasARecord = checkdnsrr($ok, 'A');

        // Kiểm tra Ping
        $pingResult = $this->checkDomainByPing($ok);

        // Kiểm tra WHOIS
        $whoisResult = $this->checkWhoisVietnam($ok);

        // Đếm bằng chứng
        $strongEvidence = 0;
        if ($hasARecord) $strongEvidence++;
        if ($pingResult === true) $strongEvidence++;
        if ($whoisResult === true) $strongEvidence++;

        // Nếu có ít nhất 2 bằng chứng → đã đăng ký
        if ($strongEvidence >= 2) {
            return response()->json([
                'success' => false,
                'message' => "Tên Miền {$ok} Đã Được Đăng Ký!",
                'html' => '<script>toastr.error("Tên Miền ' . $ok . ' Đã Được Đăng Ký!", "Thông Báo");</script>'
            ]);
        }

        // Có thể đăng ký
        $checkoutUrl = route('domain.checkout', ['domain' => $ok]);
        $html = '<script>toastr.success("Bạn Có Thể Mua Miền ' . $ok . ' Ngay Bây Giờ", "Thông Báo");</script>';
        $html .= '<center><b class="text-danger">Bạn Có Thể Đăng Ký Tên Miền Này Ngay Bây Giờ <a href="' . $checkoutUrl . '" class="text-success">Tại Đây</a></b><br><br></center>';

        return response()->json([
            'success' => true,
            'message' => "Bạn Có Thể Mua Miền {$ok} Ngay Bây Giờ",
            'html' => $html
        ]);
    }

    private function checkDomainByPing($domain)
    {
        $ip = gethostbyname($domain);
        
        if ($ip === $domain) {
            return false;
        }
        
        $excludeIPs = [
            '127.0.0.1', '0.0.0.0', '8.8.8.8', '1.1.1.1',
            '208.67.222.222', '74.125.224.72', '173.194.44.0',
            '216.58.192.0', '104.21.0.0', '172.67.0.0',
            '141.101.0.0', '162.158.0.0', '198.41.0.0', '188.114.0.0',
        ];
        
        foreach ($excludeIPs as $excludeIP) {
            if ($ip === $excludeIP || strpos($ip, substr($excludeIP, 0, 8)) === 0) {
                return false;
            }
        }
        
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
        
        $majorWebsiteIPs = [
            '142.250.0.0', '157.240.0.0', '31.13.0.0', '66.220.0.0',
            '69.63.0.0', '104.244.0.0', '151.101.0.0', '13.107.0.0',
            '52.84.0.0', '104.16.0.0', '172.64.0.0', '198.41.0.0',
        ];
        
        foreach ($majorWebsiteIPs as $majorIP) {
            if (strpos($ip, substr($majorIP, 0, 8)) === 0) {
                return true;
            }
        }
        
        return false;
    }

    private function checkWhoisVietnam($domain)
    {
        $url = "https://domain.inet.vn/api/whois?domain=" . urlencode($domain);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        
        $check = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode != 200 || !$check) {
            return null;
        }
        
        $checkLower = strtolower($check);
        
        $strongRegisteredPhrases = [
            'registry expiry date:', 'expiration date:', 'registration date:',
            'created:', 'registrar:', 'domain status: ok', 'domain status: active',
        ];
        
        foreach ($strongRegisteredPhrases as $phrase) {
            if (strpos($checkLower, $phrase) !== false) {
                return true;
            }
        }
        
        $availablePhrases = [
            'no match', 'not found', 'no data found', 'không tìm thấy',
            'chưa được đăng ký', 'domain not found', 'no entries found'
        ];
        
        foreach ($availablePhrases as $phrase) {
            if (strpos($checkLower, $phrase) !== false) {
                return false;
            }
        }
        
        return null;
    }

    /**
     * Mua domain
     */
    /**
     * Generate unique MGD (transaction ID)
     */
    private function generateMGD()
    {
        do {
            $mgd = time() . rand(1000, 9999);
        } while (
            History::where('mgd', $mgd)->exists() ||
            HostingHistory::where('mgd', $mgd)->exists() ||
            VPSHistory::where('mgd', $mgd)->exists() ||
            SourceCodeHistory::where('mgd', $mgd)->exists()
        );
        return (string)$mgd;
    }

    public function buyDomain(Request $request)
    {
        $domain = $request->input('domain', '');
        $ns1 = $request->input('ns1', '');
        $ns2 = $request->input('ns2', '');
        $hsd = $request->input('hsd', '');
        $mgd = $this->generateMGD();

        if ($domain == "" || $ns1 == "" || $ns2 == "" || $hsd == "") {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Đầy Đủ Thông Tin',
                'html' => '<script>toastr.error("Vui Lòng Nhập Đầy Đủ Thông Tin", "Thông Báo");</script>'
            ]);
        }

        // Lấy đuôi miền
        $explode = explode(".", $domain);
        $duoimien = isset($explode[1]) ? '.' . $explode[1] : '';

        // Fetch domain info
        $domainInfo = Domain::findByDuoi($duoimien);
        if (!$domainInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin đuôi miền!',
                'html' => '<script>toastr.error("Không tìm thấy thông tin đuôi miền!", "Thông Báo");</script>'
            ]);
        }

        // Check if already purchased
        $checkls = History::where('domain', $domain)->first();

        $tienphaitra = $domainInfo->price;

        // Validate price
        if ($tienphaitra <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Giá tiền không hợp lệ!',
                'html' => '<script>toastr.error("Giá tiền không hợp lệ!", "Thông Báo");</script>'
            ]);
        }

        if ($hsd == '1') {
            // Check if user is logged in - sử dụng $request->session()
            if (!$request->hasSession() || !$request->session()->has('users')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!',
                    'html' => '<script>toastr.error("Vui Lòng Đăng Nhập Để Thực Hiện!", "Thông Báo");</script>'
                ]);
            }

            $user = User::findByUsername($request->session()->get('users'));
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin tài khoản!',
                    'html' => '<script>toastr.error("Không tìm thấy thông tin tài khoản!", "Thông Báo");</script>'
                ]);
            }

            // Check if domain already exists
            if ($checkls) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn Hàng Này Đã Thanh Toán, Chờ Xử Lí!',
                    'html' => '<script>toastr.error("Đơn Hàng Này Đã Thanh Toán, Chờ Xử Lí!", "Thông Báo");</script>'
                ]);
            }

            if ($user->tien >= $tienphaitra) {
                DB::beginTransaction();
                try {
                    $time = date('Y-m-d H:i:s');
                    
                    $history = new History();
                    $history->uid = $user->id;
                    $history->domain = $domain;
                    $history->ns1 = $ns1;
                    $history->ns2 = $ns2;
                    $history->hsd = (int)$hsd;
                    $history->status = 0;
                    $history->mgd = $mgd;
                    $history->time = $time;
                    $history->timedns = '0';
                    
                    if ($history->save()) {
                        $user->incrementBalance(-1 * (int)$tienphaitra);
                        DB::commit();
                        
                        // Send Telegram notification to admin
                        try {
                            $this->telegramService->notifyNewOrder('domain', [
                                'username' => $user->taikhoan,
                                'mgd' => (string)$mgd,
                                'domain' => $domain,
                                'ns1' => $ns1,
                                'ns2' => $ns2,
                                'time' => date('d/m/Y - H:i:s')
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Telegram error for domain order ' . $mgd . ': ' . $e->getMessage());
                        }
                        
                        // Send email confirmation to user
                        if ($user->email) {
                            try {
                                Log::info('Sending domain order confirmation email (AjaxController)', [
                                    'user_email' => $user->email,
                                    'mgd' => $mgd,
                                    'domain' => $domain
                                ]);
                                
                                Mail::to($user->email)->send(new \App\Mail\OrderConfirmationMail(
                                    $history,
                                    'domain',
                                    $user,
                                    [
                                        'price' => $tienphaitra,
                                        'domain' => $domain,
                                        'ns1' => $ns1,
                                        'ns2' => $ns2,
                                    ]
                                ));
                                
                                Log::info('Domain order confirmation email sent successfully (AjaxController)', [
                                    'user_email' => $user->email,
                                    'mgd' => $mgd
                                ]);
                            } catch (\Exception $e) {
                                Log::error('Domain order email error (AjaxController)', [
                                    'user_email' => $user->email,
                                    'mgd' => $mgd,
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString()
                                ]);
                            }
                        } else {
                            Log::warning('Domain order - User has no email (AjaxController)', [
                                'user_id' => $user->id,
                                'username' => $user->taikhoan,
                                'mgd' => $mgd
                            ]);
                        }
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Mua Tên Miền Thành Công, Chờ Xử Lí!',
                            'html' => '<script>toastr.success("Mua Tên Miền Thành Công, Chờ Xử Lí!", "Thông Báo");</script>'
                        ]);
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Không Thể Mua Vào Lúc Này!',
                            'html' => '<script>toastr.error("Không Thể Mua Vào Lúc Này!", "Thông Báo");</script>'
                        ]);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error buying domain: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Có lỗi xảy ra, vui lòng thử lại!',
                        'html' => '<script>toastr.error("Có lỗi xảy ra, vui lòng thử lại!", "Thông Báo");</script>'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Số Dư Tài Khoản Không Đủ!',
                    'html' => '<script>toastr.error("Số Dư Tài Khoản Không Đủ!", "Thông Báo");</script>'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Hạn Sử Dụng Không Hợp Lệ!',
                'html' => '<script>toastr.error("Hạn Sử Dụng Không Hợp Lệ!", "Thông Báo");</script>'
            ]);
        }
    }

    /**
     * Mua hosting
     */
    public function buyHosting(Request $request)
    {
        $hostingId = $request->input('hosting_id', 0);
        $period = $request->input('period', '');
        $mgd = $this->generateMGD();

        if ($hostingId == 0 || $period == "") {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Đầy Đủ Thông Tin!',
                'html' => '<script>toastr.error("Vui Lòng Nhập Đầy Đủ Thông Tin!", "Thông Báo");</script>'
            ]);
        }

        if (!in_array($period, ['month', 'year'])) {
            return response()->json([
                'success' => false,
                'message' => 'Thời gian thuê không hợp lệ!',
                'html' => '<script>toastr.error("Thời gian thuê không hợp lệ!", "Thông Báo");</script>'
            ]);
        }

        $hosting = Hosting::find($hostingId);
        if (!$hosting) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy gói hosting!',
                'html' => '<script>toastr.error("Không tìm thấy gói hosting!", "Thông Báo");</script>'
            ]);
        }

        $tienphaitra = $period === 'month' ? $hosting->price_month : $hosting->price_year;

        // Validate price
        if ($tienphaitra <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Giá tiền không hợp lệ!',
                'html' => '<script>toastr.error("Giá tiền không hợp lệ!", "Thông Báo");</script>'
            ]);
        }

        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!',
                'html' => '<script>toastr.error("Vui Lòng Đăng Nhập Để Thực Hiện!", "Thông Báo");</script>'
            ]);
        }

        $user = User::findByUsername($request->session()->get('users'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!',
                'html' => '<script>toastr.error("Không tìm thấy thông tin tài khoản!", "Thông Báo");</script>'
            ]);
        }

        if ($user->tien >= $tienphaitra) {
            DB::beginTransaction();
            try {
                $time = date('Y-m-d H:i:s');
                
                $history = new HostingHistory();
                $history->uid = $user->id;
                $history->hosting_id = $hostingId;
                $history->period = $period;
                $history->mgd = $mgd;
                $history->status = 1;
                $history->time = $time;
                
                if ($history->save()) {
                    $user->incrementBalance(-1 * (int)$tienphaitra);
                    DB::commit();
                    
                    $contactUrl = route('contact-admin', ['type' => 'hosting', 'mgd' => $mgd]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Mua Hosting Thành Công!',
                        'html' => '<script>toastr.success("Mua Hosting Thành Công!", "Thông Báo");</script><script>setTimeout(function(){ window.location.href = "' . $contactUrl . '"; }, 1500);</script>',
                        'redirect' => $contactUrl
                    ]);
                } else {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Không Thể Mua Vào Lúc Này!',
                        'html' => '<script>toastr.error("Không Thể Mua Vào Lúc Này!", "Thông Báo");</script>'
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error buying hosting: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại!',
                    'html' => '<script>toastr.error("Có lỗi xảy ra, vui lòng thử lại!", "Thông Báo");</script>'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!',
                'html' => '<script>toastr.error("Số Dư Tài Khoản Không Đủ!", "Thông Báo");</script>'
            ]);
        }
    }

    /**
     * Mua VPS
     */
    public function buyVPS(Request $request)
    {
        $vpsId = $request->input('vps_id', 0);
        $period = $request->input('period', '');
        $mgd = $this->generateMGD();

        if ($vpsId == 0 || $period == "") {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Đầy Đủ Thông Tin!',
                'html' => '<script>toastr.error("Vui Lòng Nhập Đầy Đủ Thông Tin!", "Thông Báo");</script>'
            ]);
        }

        if (!in_array($period, ['month', 'year'])) {
            return response()->json([
                'success' => false,
                'message' => 'Thời gian thuê không hợp lệ!',
                'html' => '<script>toastr.error("Thời gian thuê không hợp lệ!", "Thông Báo");</script>'
            ]);
        }

        $vps = VPS::find($vpsId);
        if (!$vps) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy gói VPS!',
                'html' => '<script>toastr.error("Không tìm thấy gói VPS!", "Thông Báo");</script>'
            ]);
        }

        $tienphaitra = $period === 'month' ? $vps->price_month : $vps->price_year;

        // Validate price
        if ($tienphaitra <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Giá tiền không hợp lệ!',
                'html' => '<script>toastr.error("Giá tiền không hợp lệ!", "Thông Báo");</script>'
            ]);
        }

        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!',
                'html' => '<script>toastr.error("Vui Lòng Đăng Nhập Để Thực Hiện!", "Thông Báo");</script>'
            ]);
        }

        $user = User::findByUsername($request->session()->get('users'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!',
                'html' => '<script>toastr.error("Không tìm thấy thông tin tài khoản!", "Thông Báo");</script>'
            ]);
        }

        if ($user->tien >= $tienphaitra) {
            DB::beginTransaction();
            try {
                $time = date('Y-m-d H:i:s');
                
                $history = new VPSHistory();
                $history->uid = $user->id;
                $history->vps_id = $vpsId;
                $history->period = $period;
                $history->mgd = $mgd;
                $history->status = 1;
                $history->time = $time;
                
                if ($history->save()) {
                    $user->incrementBalance(-1 * (int)$tienphaitra);
                    DB::commit();
                    
                    $contactUrl = route('contact-admin', ['type' => 'vps', 'mgd' => $mgd]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Mua VPS Thành Công!',
                        'html' => '<script>toastr.success("Mua VPS Thành Công!", "Thông Báo");</script><script>setTimeout(function(){ window.location.href = "' . $contactUrl . '"; }, 1500);</script>',
                        'redirect' => $contactUrl
                    ]);
                } else {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Không Thể Mua Vào Lúc Này!',
                        'html' => '<script>toastr.error("Không Thể Mua Vào Lúc Này!", "Thông Báo");</script>'
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error buying VPS: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại!',
                    'html' => '<script>toastr.error("Có lỗi xảy ra, vui lòng thử lại!", "Thông Báo");</script>'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!',
                'html' => '<script>toastr.error("Số Dư Tài Khoản Không Đủ!", "Thông Báo");</script>'
            ]);
        }
    }

    /**
     * Mua source code
     */
    public function buySourceCode(Request $request)
    {
        $sourceCodeId = $request->input('source_code_id', 0);
        $mgd = $this->generateMGD();

        if ($sourceCodeId == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Chọn Source Code!',
                'html' => '<script>toastr.error("Vui Lòng Chọn Source Code!", "Thông Báo");</script>'
            ]);
        }

        $sourceCode = SourceCode::find($sourceCodeId);
        if (!$sourceCode) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy source code!',
                'html' => '<script>toastr.error("Không tìm thấy source code!", "Thông Báo");</script>'
            ]);
        }

        $tienphaitra = $sourceCode->price;

        // Validate price
        if ($tienphaitra <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Giá tiền không hợp lệ!',
                'html' => '<script>toastr.error("Giá tiền không hợp lệ!", "Thông Báo");</script>'
            ]);
        }

        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!',
                'html' => '<script>toastr.error("Vui Lòng Đăng Nhập Để Thực Hiện!", "Thông Báo");</script>'
            ]);
        }

        $user = User::findByUsername($request->session()->get('users'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!',
                'html' => '<script>toastr.error("Không tìm thấy thông tin tài khoản!", "Thông Báo");</script>'
            ]);
        }

        if ($user->tien >= $tienphaitra) {
            DB::beginTransaction();
            try {
                $time = date('Y-m-d H:i:s');
                
                $history = new SourceCodeHistory();
                $history->uid = $user->id;
                $history->source_code_id = $sourceCodeId;
                $history->mgd = $mgd;
                $history->status = 1;
                $history->time = $time;
                
                if ($history->save()) {
                    $user->incrementBalance(-1 * (int)$tienphaitra);
                    DB::commit();
                    
                    $downloadUrl = route('download.index', ['mgd' => $mgd]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Mua Source Code Thành Công!',
                        'html' => '<script>toastr.success("Mua Source Code Thành Công!", "Thông Báo");</script><script>setTimeout(function(){ window.location.href = "' . $downloadUrl . '"; }, 1500);</script>',
                        'redirect' => $downloadUrl
                    ]);
                } else {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Không Thể Mua Vào Lúc Này!',
                        'html' => '<script>toastr.error("Không Thể Mua Vào Lúc Này!", "Thông Báo");</script>'
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error buying source code: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại!',
                    'html' => '<script>toastr.error("Có lỗi xảy ra, vui lòng thử lại!", "Thông Báo");</script>'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!',
                'html' => '<script>toastr.error("Số Dư Tài Khoản Không Đủ!", "Thông Báo");</script>'
            ]);
        }
    }

    /**
     * Update DNS
     */
    public function updateDns(Request $request)
    {
        if (!$request->hasSession() || !$request->session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập!',
                'html' => '<script>toastr.error("Vui lòng đăng nhập!", "Thông Báo");</script>'
            ]);
        }

        $ns1 = $request->input('ns1');
        $ns2 = $request->input('ns2');
        $mgd = $request->input('mgd');

        $user = User::findByUsername($request->session()->get('users'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng!',
                'html' => '<script>toastr.error("Không tìm thấy thông tin người dùng!", "Thông Báo");</script>'
            ]);
        }

        $checkmgd = History::where('uid', $user->id)
            ->where('mgd', $mgd)
            ->first();

        if (!$checkmgd) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền quản lý tên miền này!',
                'html' => '<script>toastr.error("Bạn không có quyền quản lý tên miền này!", "Thông Báo");</script>'
            ]);
        }

        if ($ns1 == "" || $ns2 == "") {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Đầy Đủ Thông Tin!',
                'html' => '<script>toastr.error("Vui Lòng Nhập Đầy Đủ Thông Tin!", "Thông Báo");</script>'
            ]);
        }

        if ($checkmgd->timedns == '0') {
            $today = date('d/m/Y');
            $ex = explode("/", $today);
            $chuky = ($ex[0] + 15) . '/' . $ex[1] . '/' . $ex[2];

            $checkmgd->ns1 = $ns1;
            $checkmgd->ns2 = $ns2;
            $checkmgd->timedns = $chuky;
            $checkmgd->save();

            return response()->json([
                'success' => true,
                'message' => 'Thay Đổi DNS Thành Công, Vui Lòng Chờ 12h - 24h Để DNS Mới Hoạt Động',
                'html' => '<script>toastr.success("Thay Đổi DNS Thành Công, Vui Lòng Chờ 12h - 24h Để DNS Mới Hoạt Động", "Thông Báo");</script>'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Bạn Không Thể Cập Nhật Thông Tin Ngay Bây Giờ Vui Lòng Đợi Chu Kỳ 15 Qua!',
                'html' => '<script>toastr.error("Bạn Không Thể Cập Nhật Thông Tin Ngay Bây Giờ Vui Lòng Đợi Chu Kỳ 15 Qua!", "Thông Báo");</script>'
            ]);
        }
    }

    /**
     * Recharge card
     */
    public function rechargeCard(Request $request)
    {
        $pin = trim($request->input('pin', ''));
        $serial = trim($request->input('serial', ''));
        $amount = trim($request->input('amount', ''));
        $type = trim($request->input('type', ''));
        $requestid = (string)time() . rand(500000, 999999);

        $time = date('Y-m-d H:i:s');
        $time2 = date('Y-m-d');

        // Lấy cấu hình từ DB
        $settings = Settings::getOne();
        $apikey = $settings->apikey ?? '';
        $callback = $settings->callback ?? (config('app.url') . '/callback');

        // Xác định user hiện tại
        $user_id = 0;
        if ($request->hasSession() && $request->session()->has('users')) {
            $user = User::findByUsername($request->session()->get('users'));
            $user_id = $user ? $user->id : 0;
        }

        // Validate cơ bản
        $allowedTypes = ['VIETTEL', 'VINAPHONE', 'MOBIFONE', 'GATE', 'ZING', 'VNMOBI', 'VIETNAMMOBILE'];
        
        if ($pin === "" || $serial === "" || $amount === "" || $type === "") {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Nhập Đầy Đủ Thông Tin!',
                'html' => '<script>toastr.error("Vui Lòng Nhập Đầy Đủ Thông Tin!", "Thông Báo");</script>'
            ]);
        }

        if (!ctype_digit($amount)) {
            return response()->json([
                'success' => false,
                'message' => 'Mệnh giá không hợp lệ',
                'html' => '<script>toastr.error("Mệnh giá không hợp lệ", "Thông Báo");</script>'
            ]);
        }

        if (!in_array(strtoupper($type), $allowedTypes, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Loại thẻ không hỗ trợ',
                'html' => '<script>toastr.error("Loại thẻ không hỗ trợ", "Thông Báo");</script>'
            ]);
        }

        if ($user_id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập lại để nạp thẻ',
                'html' => '<script>toastr.error("Vui lòng đăng nhập lại để nạp thẻ", "Thông Báo");</script>'
            ]);
        }

        // Kiểm tra thẻ đã tồn tại
        $existingCard = Card::where('pin', $pin)
            ->where('serial', $serial)
            ->first();

        if ($existingCard) {
            return response()->json([
                'success' => false,
                'message' => 'Thẻ Đã Tồn Tại Trong Hệ Thống!',
                'html' => '<script>toastr.error("Thẻ Đã Tồn Tại Trong Hệ Thống!");</script>'
            ]);
        }

        // Gửi request đến cardvip API
        $dataPost = [
            'APIKey' => $apikey,
            'NetworkCode' => $type,
            'PricesExchange' => $amount,
            'NumberCard' => $pin,
            'SeriCard' => $serial,
            'IsFast' => true,
            'RequestId' => $requestid,
            'UrlCallback' => $callback
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://partner.cardvip.vn/api/createExchange",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($dataPost),
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        ]);

        $response = curl_exec($curl);
        $curlErr = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response === false) {
            $msg = $curlErr !== '' ? $curlErr : 'Không thể kết nối cổng nạp thẻ';
            return response()->json([
                'success' => false,
                'message' => $msg,
                'html' => '<script>toastr.error("' . $msg . '", "Thông Báo");</script>'
            ]);
        }

        $obj = json_decode($response, true);
        if (!is_array($obj)) {
            return response()->json([
                'success' => false,
                'message' => 'API trả về dữ liệu không hợp lệ (HTTP ' . $httpCode . ')',
                'html' => '<script>toastr.error("API trả về dữ liệu không hợp lệ (HTTP ' . $httpCode . ')", "Thông Báo");</script>'
            ]);
        }

        $status = $obj['status'] ?? null;
        $message = $obj['message'] ?? '';

        if ($status === 200) {
            // Lưu card vào database
            $card = new Card();
            $card->uid = $user_id;
            $card->pin = $pin;
            $card->serial = $serial;
            $card->type = strtoupper($type);
            $card->amount = (string)$amount;
            $card->requestid = (string)$requestid;
            $card->status = 0;
            $card->time = $time;
            $card->time2 = $time2;
            $card->save();

            return response()->json([
                'success' => true,
                'message' => 'Nạp thẻ thành công, vui lòng chờ 30s - 1 phút để duyệt',
                'html' => '<script>toastr.success("Nạp thẻ thành công, vui lòng chờ 30s - 1 phút để duyệt", "Thông Báo");</script>'
            ]);
        } elseif ($status === 400) {
            return response()->json([
                'success' => false,
                'message' => 'Thẻ đã tồn tại hoặc không hợp lệ: ' . htmlspecialchars($message),
                'html' => '<script>toastr.error("Thẻ đã tồn tại hoặc không hợp lệ: ' . htmlspecialchars($message) . '", "Thông Báo");</script>'
            ]);
        } elseif ($status === 401) {
            return response()->json([
                'success' => false,
                'message' => 'Sai định dạng thẻ: ' . htmlspecialchars($message),
                'html' => '<script>toastr.error("Sai định dạng thẻ: ' . htmlspecialchars($message) . '", "Thông Báo");</script>'
            ]);
        } elseif ($status === 403) {
            return response()->json([
                'success' => false,
                'message' => 'APIKey không hợp lệ hoặc bị hạn chế',
                'html' => '<script>toastr.error("APIKey không hợp lệ hoặc bị hạn chế", "Thông Báo");</script>'
            ]);
        } else {
            $safeMsg = $message !== '' ? htmlspecialchars($message) : 'Có lỗi khi gửi thẻ (HTTP ' . $httpCode . ')';
            return response()->json([
                'success' => false,
                'message' => $safeMsg,
                'html' => '<script>toastr.error("' . $safeMsg . '", "Thông Báo");</script>'
            ]);
        }
    }
}
