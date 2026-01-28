<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model và Service cần thiết
use App\Models\Card; // Model quản lý thẻ cào
use App\Models\Settings; // Model quản lý cài đặt hệ thống
use App\Services\PaymentService; // Service xử lý thanh toán thẻ cào
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Auth; // Facade để xác thực (không dùng trong code này)
use Illuminate\Support\Facades\Log; // Facade để ghi log

/**
 * Class PaymentController
 * Controller xử lý nạp tiền bằng thẻ cào
 */
class PaymentController extends Controller
{
    // Thuộc tính lưu trữ instance của PaymentService
    protected $paymentService;

    /**
     * Hàm khởi tạo (Constructor)
     * Dependency Injection: Laravel tự động inject PaymentService vào đây
     * 
     * @param PaymentService $paymentService - Service để xử lý thanh toán thẻ cào
     */
    public function __construct(PaymentService $paymentService)
    {
        // Gán PaymentService vào thuộc tính của class
        $this->paymentService = $paymentService;
    }

    /**
     * Hiển thị trang nạp thẻ
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function recharge()
    {
        // Lấy username từ session
        $username = session('users');
        // Nếu chưa đăng nhập, redirect đến trang đăng nhập
        if (!$username) {
            return redirect()->route('login');
        }

        // Tìm user trong database theo username
        $user = \App\Models\User::where('taikhoan', $username)->first();
        // Nếu không tìm thấy user, redirect đến trang đăng nhập
        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy lịch sử nạp thẻ của user, sắp xếp theo ID giảm dần (mới nhất trước)
        $cards = Card::where('uid', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        // Lấy cài đặt hệ thống để hiển thị thông tin
        $settings = Settings::first();

        // Trả về view với dữ liệu user, lịch sử thẻ và settings
        return view('pages.recharge', compact('user', 'cards', 'settings'));
    }

    /**
     * Xử lý nạp thẻ cào
     * 
     * @param Request $request - HTTP request chứa pin, serial, amount, type
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function processRecharge(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'pin' => 'required|string|max:30', // Mã PIN thẻ cào, bắt buộc, tối đa 30 ký tự
            'serial' => 'required|string|max:30', // Serial thẻ cào, bắt buộc, tối đa 30 ký tự
            'amount' => 'required|integer|in:10000,20000,30000,50000,100000,200000,300000,500000,1000000', 
            // Mệnh giá thẻ, bắt buộc, chỉ nhận các giá trị trong danh sách
            'type' => 'required|string|in:VIETTEL,VINAPHONE,VIETNAMOBILE,MOBIFONE,GARENA,ZING,GATE,VNMOBI' 
            // Loại thẻ, bắt buộc, chỉ nhận các loại trong danh sách
        ], [
            'type.in' => 'Loại thẻ không hợp lệ. Vui lòng chọn lại loại thẻ.',
            'type.required' => 'Vui lòng chọn loại thẻ.',
            'amount.in' => 'Mệnh giá thẻ không hợp lệ. Vui lòng chọn lại mệnh giá.',
            'amount.required' => 'Vui lòng chọn mệnh giá thẻ.',
            'pin.required' => 'Vui lòng nhập mã thẻ.',
            'serial.required' => 'Vui lòng nhập số seri thẻ.'
        ]);

        // Lấy username từ session
        $username = session('users');
        // Nếu chưa đăng nhập, trả về JSON response lỗi
        if (!$username) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập lại để nạp thẻ'
            ]);
        }

        // Tìm user trong database theo username
        $user = \App\Models\User::where('taikhoan', $username)->first();
        // Nếu không tìm thấy user, trả về JSON response lỗi
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ]);
        }

        // Xử lý nạp thẻ thông qua PaymentService
        $result = $this->paymentService->rechargeCard(
            $request->serial, // Serial thẻ
            $request->pin, // PIN thẻ
            $request->type, // Loại thẻ
            $request->amount, // Mệnh giá
            $user->id // ID người dùng
        );

        // Trả về kết quả dưới dạng JSON
        return response()->json($result);
    }

    /**
     * Xử lý nạp thẻ cào (AJAX endpoint - legacy compatibility)
     * Alias cho processRecharge() để tương thích ngược với code cũ
     * 
     * @param Request $request - HTTP request chứa thông tin thẻ
     * @return \Illuminate\Http\JsonResponse - JSON response cho AJAX
     */
    public function processCard(Request $request)
    {
        // Gọi lại hàm processRecharge()
        return $this->processRecharge($request);
    }

    /**
     * Handle callback from cardvip.vn
     */
    public function callback(Request $request)
    {
        // Log callback for debugging
        Log::info('CardVIP Callback Received', $request->all());

        // Get callback data (format mới: request_id, status, amount, value, declared_value, telco, code, serial)
        $data = [
            'request_id' => $request->input('request_id') ?: $request->input('requestid'), // Format mới hoặc cũ
            'status' => $request->input('status'),
            'amount' => $request->input('amount'),
            'value' => $request->input('value'),
            'declared_value' => $request->input('declared_value'),
            'telco' => $request->input('telco'),
            'code' => $request->input('code'),
            'serial' => $request->input('serial'),
            // Fallback cho format cũ
            'pricesvalue' => $request->input('pricesvalue'),
            'value_receive' => $request->input('value_receive'),
            'card_code' => $request->input('card_code'),
            'card_seri' => $request->input('card_seri'),
            'value_customer_receive' => $request->input('value_customer_receive'),
            'requestid' => $request->input('requestid')
        ];

        // Verify callback
        if (!$this->paymentService->verifyCallback($data)) {
            Log::warning('Invalid callback data', $data);
            return response('Invalid callback', 400);
        }

        // Process callback
        $result = $this->paymentService->processCallback($data);

        if ($result['success']) {
            return response('OK', 200);
        } else {
            return response($result['message'], 400);
        }
    }
}
