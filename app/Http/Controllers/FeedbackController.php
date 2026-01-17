<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model và Service cần thiết
use App\Models\Feedback; // Model quản lý feedback
use App\Models\User; // Model quản lý người dùng
use App\Models\Settings; // Model quản lý cài đặt hệ thống
use App\Services\TelegramService; // Service gửi thông báo Telegram
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class FeedbackController
 * Controller xử lý phản hồi/liên hệ từ người dùng
 */
class FeedbackController extends Controller
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
     * Hiển thị form phản hồi và lịch sử feedback của user
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        // Lấy thông tin user hiện tại từ database
        $user = User::where('taikhoan', session('users'))->first();
        // Nếu không tìm thấy user, redirect đến trang đăng nhập
        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy lịch sử feedback của user, sắp xếp theo ID giảm dần (mới nhất trước)
        $userFeedbacks = Feedback::where('uid', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        // Đếm số tin nhắn chưa đọc (status = 1 nghĩa là admin đã phản hồi nhưng user chưa đọc)
        $unreadCount = Feedback::where('uid', $user->id)
            ->where('status', 1) // Đã được admin phản hồi
            ->whereNotNull('admin_reply') // Có phản hồi từ admin
            ->count();

        // Trả về view với dữ liệu user, lịch sử feedback và số tin nhắn chưa đọc
        return view('pages.feedback', compact('user', 'userFeedbacks', 'unreadCount'));
    }

    /**
     * Lưu feedback mới từ user
     * 
     * @param Request $request - HTTP request chứa email và message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        // Lấy thông tin user hiện tại từ database
        $user = User::where('taikhoan', session('users'))->first();
        // Nếu không tìm thấy user, redirect với thông báo lỗi
        if (!$user) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin tài khoản!');
        }

        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'email' => 'required|email', // Email bắt buộc, định dạng email hợp lệ
            'message' => 'required|string|min:10' // Message bắt buộc, kiểu string, tối thiểu 10 ký tự
        ], [
            // Thông báo lỗi tùy chỉnh
            'email.required' => 'Vui lòng nhập email liên hệ!',
            'email.email' => 'Email không hợp lệ!',
            'message.required' => 'Vui lòng nhập nội dung phản hồi!',
            'message.min' => 'Nội dung phản hồi phải có ít nhất 10 ký tự!'
        ]);

        // Tạo chuỗi thời gian định dạng Việt Nam
        $time = date('d/m/Y - H:i:s');
        // Tạo feedback mới trong database
        $feedback = Feedback::create([
            'uid' => $user->id, // ID người dùng
            'username' => $user->taikhoan, // Username
            'email' => $request->email, // Email từ form
            'message' => $request->message, // Nội dung phản hồi từ form
            'status' => 0, // Trạng thái: 0 = Chờ xử lý (pending)
            'time' => $time // Thời gian gửi feedback
        ]);

        // Gửi thông báo Telegram cho admin về feedback mới
        $this->telegramService->notifyNewFeedback([
            'feedback_id' => $feedback->id, // ID feedback để tick xác nhận
            'username' => $user->taikhoan, // Username người gửi
            'email' => $request->email, // Email người gửi
            'title' => 'Phản hồi từ khách hàng', // Tiêu đề
            'content' => $request->message, // Nội dung phản hồi
            'time' => $time // Thời gian
        ]);

        // Redirect về trang feedback với thông báo thành công
        return redirect()->route('feedback.index')
            ->with('success', 'Phản hồi của bạn đã được gửi! Chúng tôi sẽ xem xét và phản hồi sớm nhất có thể.');
    }
}
