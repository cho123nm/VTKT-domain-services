<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\History;
use App\Models\Hosting;
use App\Models\HostingHistory;
use App\Models\VPS;
use App\Models\VPSHistory;
use App\Models\SourceCode;
use App\Models\SourceCodeHistory;
use App\Models\User;
use App\Services\TelegramService;
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

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
    /**
     * Show domain checkout page
     */
    public function domain(Request $request)
    {
        $domainName = $request->query('domain');
        
        if (empty($domainName)) {
            return redirect()->route('home');
        }

        // Extract domain extension
        $parts = explode('.', $domainName);
        if (count($parts) < 2) {
            return redirect()->route('home');
        }
        
        $extension = '.' . $parts[1];
        
        // Find domain type
        $domain = Domain::findByDuoi($extension);
        
        if (!$domain) {
            return redirect()->route('home');
        }

        return view('pages.checkout.domain', [
            'domainName' => $domainName,
            'domain' => $domain,
            'price' => $domain->price
        ]);
    }

    /**
     * Show hosting checkout page
     */
    public function hosting(Request $request)
    {
        // Check if user is logged in
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        $id = $request->query('id', 0);
        
        if ($id == 0) {
            return redirect()->route('hosting.index');
        }

        $hosting = Hosting::find($id);
        
        if (!$hosting) {
            return redirect()->route('hosting.index');
        }

        return view('pages.checkout.hosting', [
            'hosting' => $hosting
        ]);
    }

    /**
     * Show VPS checkout page
     */
    public function vps(Request $request)
    {
        // Check if user is logged in
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        $id = $request->query('id', 0);
        
        if ($id == 0) {
            return redirect()->route('vps.index');
        }

        $vps = VPS::find($id);
        
        if (!$vps) {
            return redirect()->route('vps.index');
        }

        return view('pages.checkout.vps', [
            'vps' => $vps
        ]);
    }

    /**
     * Show source code checkout page
     */
    public function sourcecode(Request $request)
    {
        // Check if user is logged in
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        $id = $request->query('id', 0);
        
        if ($id == 0) {
            return redirect()->route('sourcecode.index');
        }

        $sourceCode = SourceCode::find($id);
        
        if (!$sourceCode) {
            return redirect()->route('sourcecode.index');
        }

        return view('pages.checkout.sourcecode', [
            'sourceCode' => $sourceCode
        ]);
    }

    /**
     * Process domain purchase
     */
    public function processDomain(Request $request)
    {
        // Đảm bảo session được khởi động
        if (!$request->hasSession()) {
            Log::warning('ProcessDomain - No session available', [
                'url' => $request->fullUrl(),
                'cookie_header' => $request->header('Cookie')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Phiên làm việc đã hết hạn, vui lòng tải lại trang và đăng nhập lại!'
            ]);
        }
        
        // Log ngay từ đầu để đảm bảo request đến được controller
        Log::info('ProcessDomain - Request received', [
            'method' => $request->method(),
            'path' => $request->path(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'has_cookie' => $request->hasHeader('Cookie'),
            'cookie_header' => $request->header('Cookie') ? 'present' : 'missing',
            'has_session' => $request->hasSession(),
            'session_id' => $request->session()->getId()
        ]);
        
        // Đảm bảo session được load lại từ storage
        // Không cần reflash, session đã được middleware xử lý
        
        // Debug session - kiểm tra nhiều cách
        $sessionUsers = $request->session()->get('users');
        $hasUsers = $request->session()->has('users');
        $allSession = $request->session()->all();
        $sessionId = $request->session()->getId();
        
        Log::info('ProcessDomain - Session check', [
            'has_users' => $hasUsers,
            'users_value' => $sessionUsers,
            'session_id' => $sessionId,
            'session_keys' => array_keys($allSession),
            'cookie_header' => $request->header('Cookie'),
            'user_agent' => $request->header('User-Agent')
        ]);
        
        // Validate user is logged in - kiểm tra nhiều cách
        if (empty($sessionUsers) && !$hasUsers) {
            Log::warning('ProcessDomain - User not logged in', [
                'session_id' => $sessionId,
                'all_session_keys' => array_keys($allSession),
                'cookie_header' => $request->header('Cookie'),
                'session_driver' => config('session.driver')
            ]);
            
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

        // Validate input
        $request->validate([
            'domain' => 'required|string',
            'ns1' => 'required|string',
            'ns2' => 'required|string',
            'hsd' => 'required|string'
        ]);

        $domain = $request->input('domain');
        $ns1 = $request->input('ns1');
        $ns2 = $request->input('ns2');
        $hsd = $request->input('hsd');
        
        // Generate unique transaction ID
        $mgd = $this->generateMGD();

        // Extract domain extension
        $parts = explode('.', $domain);
        if (count($parts) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Tên miền không hợp lệ!'
            ]);
        }
        
        $extension = '.' . $parts[1];
        
        // Find domain type
        $domainType = Domain::findByDuoi($extension);
        
        if (!$domainType) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin đuôi miền!'
            ]);
        }

        $price = $domainType->price;

        // Get user - sử dụng biến đã lấy trước đó
        $user = User::findByUsername($sessionUsers);
        
        // Đảm bảo session vẫn còn valid
        if (!$user && $sessionUsers) {
            // Thử lại với session mới
            $sessionUsers = $request->session()->get('users');
            $user = User::findByUsername($sessionUsers);
        }
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!'
            ]);
        }

        // Check if domain already exists
        $existingOrder = History::where('domain', $domain)->first();
        
        if ($existingOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn Hàng Này Đã Thanh Toán, Chờ Xử Lí!'
            ]);
        }

        // Validate user has sufficient balance
        if ($user->tien < $price) {
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!'
            ]);
        }

        try {
            DB::beginTransaction();

            // Deduct balance
            $user->incrementBalance(-1 * (int)$price);

            // Create order
            $history = new History();
            $history->uid = $user->id;
            $history->domain = $domain;
            $history->ns1 = $ns1;
            $history->ns2 = $ns2;
            $history->hsd = $hsd;
            $history->status = 0; // Pending
            $history->mgd = (string)$mgd;
            $history->time = date('Y-m-d H:i:s');
            $history->timedns = '0';
            $history->save();

            DB::commit();

            // Send Telegram notification to admin
            $this->telegramService->notifyNewOrder('domain', [
                'username' => $user->taikhoan,
                'mgd' => (string)$mgd,
                'domain' => $domain,
                'ns1' => $ns1,
                'ns2' => $ns2,
                'time' => date('d/m/Y - H:i:s')
            ]);

            // Send email confirmation to user
            if ($user->email) {
                try {
                    Log::info('Sending domain order confirmation email', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'domain' => $domain
                    ]);
                    
                    Mail::to($user->email)->send(new OrderConfirmationMail(
                        $history,
                        'domain',
                        $user,
                        [
                            'price' => $price,
                            'domain' => $domain,
                            'ns1' => $ns1,
                            'ns2' => $ns2,
                        ]
                    ));
                    
                    Log::info('Domain order confirmation email sent successfully', [
                        'user_email' => $user->email,
                        'mgd' => $mgd
                    ]);
                } catch (\Exception $e) {
                    Log::error('Domain order email error', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Không báo lỗi cho user, chỉ log
                }
            } else {
                Log::warning('Domain order - User has no email', [
                    'user_id' => $user->id,
                    'username' => $user->taikhoan,
                    'mgd' => $mgd
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Mua Tên Miền Thành Công, Chờ Xử Lí!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Không Thể Mua Vào Lúc Này!'
            ]);
        }
    }

    /**
     * Process hosting purchase
     */
    public function processHosting(Request $request)
    {
        // Validate user is logged in
        if (!session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!'
            ]);
        }

        // Validate input
        $request->validate([
            'hosting_id' => 'required|integer',
            'period' => 'required|in:month,year'
        ]);

        $hostingId = $request->input('hosting_id');
        $period = $request->input('period');
        
        // Generate unique transaction ID
        $mgd = $this->generateMGD();

        // Find hosting package
        $hosting = Hosting::find($hostingId);
        
        if (!$hosting) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy gói hosting!'
            ]);
        }

        $price = $period === 'month' ? $hosting->price_month : $hosting->price_year;

        // Get user
        $user = User::findByUsername(session('users'));
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!'
            ]);
        }

        // Validate user has sufficient balance
        if ($user->tien < $price) {
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!'
            ]);
        }

        try {
            DB::beginTransaction();

            // Deduct balance
            $user->incrementBalance(-1 * (int)$price);

            // Create order
            $history = new HostingHistory();
            $history->uid = $user->id;
            $history->hosting_id = $hostingId;
            $history->period = $period;
            $history->mgd = (string)$mgd;
            $history->status = 1; // Approved immediately
            $history->time = date('Y-m-d H:i:s');
            $history->save();

            DB::commit();

            // Send Telegram notification to admin
            $this->telegramService->notifyNewOrder('hosting', [
                'username' => $user->taikhoan,
                'mgd' => (string)$mgd,
                'package_name' => $hosting->name,
                'period' => $period === 'month' ? '1' : '12',
                'domain' => 'N/A',
                'time' => date('d/m/Y - H:i:s')
            ]);

            // Send email confirmation to user
            if ($user->email) {
                try {
                    Log::info('Sending hosting order confirmation email', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'hosting_id' => $hostingId
                    ]);
                    
                    Mail::to($user->email)->send(new OrderConfirmationMail(
                        $history,
                        'hosting',
                        $user,
                        [
                            'price' => $price,
                            'package_name' => $hosting->name,
                            'period' => $period === 'month' ? '1' : '12',
                        ]
                    ));
                    
                    Log::info('Hosting order confirmation email sent successfully', [
                        'user_email' => $user->email,
                        'mgd' => $mgd
                    ]);
                } catch (\Exception $e) {
                    Log::error('Hosting order email error', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::warning('Hosting order - User has no email', [
                    'user_id' => $user->id,
                    'username' => $user->taikhoan,
                    'mgd' => $mgd
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Mua Hosting Thành Công!',
                'redirect' => route('contact-admin', ['type' => 'hosting', 'mgd' => $mgd])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Không Thể Mua Vào Lúc Này!'
            ]);
        }
    }

    /**
     * Process VPS purchase
     */
    public function processVPS(Request $request)
    {
        // Validate user is logged in
        if (!session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!'
            ]);
        }

        // Validate input
        $request->validate([
            'vps_id' => 'required|integer',
            'period' => 'required|in:month,year'
        ]);

        $vpsId = $request->input('vps_id');
        $period = $request->input('period');
        
        // Generate unique transaction ID
        $mgd = $this->generateMGD();

        // Find VPS package
        $vps = VPS::find($vpsId);
        
        if (!$vps) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy gói VPS!'
            ]);
        }

        $price = $period === 'month' ? $vps->price_month : $vps->price_year;

        // Get user
        $user = User::findByUsername(session('users'));
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!'
            ]);
        }

        // Validate user has sufficient balance
        if ($user->tien < $price) {
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!'
            ]);
        }

        try {
            DB::beginTransaction();

            // Deduct balance
            $user->incrementBalance(-1 * (int)$price);

            // Create order
            $history = new VPSHistory();
            $history->uid = $user->id;
            $history->vps_id = $vpsId;
            $history->period = $period;
            $history->mgd = (string)$mgd;
            $history->status = 1; // Approved immediately
            $history->time = date('Y-m-d H:i:s');
            $history->save();

            DB::commit();

            // Send Telegram notification to admin
            $this->telegramService->notifyNewOrder('vps', [
                'username' => $user->taikhoan,
                'mgd' => (string)$mgd,
                'package_name' => $vps->name,
                'period' => $period === 'month' ? '1' : '12',
                'time' => date('d/m/Y - H:i:s')
            ]);

            // Send email confirmation to user
            if ($user->email) {
                try {
                    Log::info('Sending VPS order confirmation email', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'vps_id' => $vpsId
                    ]);
                    
                    Mail::to($user->email)->send(new OrderConfirmationMail(
                        $history,
                        'vps',
                        $user,
                        [
                            'price' => $price,
                            'package_name' => $vps->name,
                            'period' => $period,
                        ]
                    ));
                    
                    Log::info('VPS order confirmation email sent successfully', [
                        'user_email' => $user->email,
                        'mgd' => $mgd
                    ]);
                } catch (\Exception $e) {
                    Log::error('VPS order email error', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::warning('VPS order - User has no email', [
                    'user_id' => $user->id,
                    'username' => $user->taikhoan,
                    'mgd' => $mgd
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Mua VPS Thành Công!',
                'redirect' => route('contact-admin', ['type' => 'vps', 'mgd' => $mgd])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Không Thể Mua Vào Lúc Này!'
            ]);
        }
    }

    /**
     * Process source code purchase
     */
    public function processSourceCode(Request $request)
    {
        // Validate user is logged in
        if (!session()->has('users')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui Lòng Đăng Nhập Để Thực Hiện!'
            ]);
        }

        // Validate input
        $request->validate([
            'source_code_id' => 'required|integer'
        ]);

        $sourceCodeId = $request->input('source_code_id');
        
        // Generate unique transaction ID
        $mgd = $this->generateMGD();

        // Find source code
        $sourceCode = SourceCode::find($sourceCodeId);
        
        if (!$sourceCode) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy source code!'
            ]);
        }

        $price = $sourceCode->price;

        // Get user
        $user = User::findByUsername(session('users'));
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin tài khoản!'
            ]);
        }

        // Validate user has sufficient balance
        if ($user->tien < $price) {
            return response()->json([
                'success' => false,
                'message' => 'Số Dư Tài Khoản Không Đủ!'
            ]);
        }

        try {
            DB::beginTransaction();

            // Deduct balance
            $user->incrementBalance(-1 * (int)$price);

            // Create order
            $history = new SourceCodeHistory();
            $history->uid = $user->id;
            $history->source_code_id = $sourceCodeId;
            $history->mgd = (string)$mgd;
            $history->status = 1; // Approved immediately
            $history->time = date('Y-m-d H:i:s');
            $history->save();

            DB::commit();

            // Prepare response data
            $responseData = [
                'success' => true,
                'message' => 'Mua Source Code Thành Công!',
                'redirect' => route('download.index', ['mgd' => $mgd])
            ];

            // Send Telegram notification to admin (non-blocking)
            try {
                $this->telegramService->notifyNewOrder('sourcecode', [
                    'username' => $user->taikhoan,
                    'mgd' => (string)$mgd,
                    'product_name' => $sourceCode->name,
                    'time' => date('d/m/Y - H:i:s')
                ]);
            } catch (\Exception $e) {
                Log::error('Telegram error for sourcecode order ' . $mgd . ': ' . $e->getMessage());
                // Không làm fail response
            }

            // Send email confirmation to user (non-blocking)
            if ($user->email) {
                try {
                    Log::info('Sending sourcecode order confirmation email', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'source_code_id' => $sourceCodeId
                    ]);
                    
                    Mail::to($user->email)->send(new OrderConfirmationMail(
                        $history,
                        'sourcecode',
                        $user,
                        [
                            'price' => $price,
                            'source_code_name' => $sourceCode->name,
                        ]
                    ));
                    
                    Log::info('Sourcecode order confirmation email sent successfully', [
                        'user_email' => $user->email,
                        'mgd' => $mgd
                    ]);
                } catch (\Exception $e) {
                    // Log lỗi email nhưng không làm fail transaction
                    Log::error('Sourcecode order email error', [
                        'user_email' => $user->email,
                        'mgd' => $mgd,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Không throw exception, tiếp tục xử lý
                }
            } else {
                Log::warning('Sourcecode order - User has no email', [
                    'user_id' => $user->id,
                    'username' => $user->taikhoan,
                    'mgd' => $mgd
                ]);
            }

            // Luôn trả về response thành công nếu transaction đã commit
            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log chi tiết lỗi để debug
            Log::error('Checkout SourceCode Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Không Thể Mua Vào Lúc Này! Vui lòng thử lại sau.'
            ]);
        }
    }
}
