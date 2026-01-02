<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use App\Models\Settings;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    /**
     * Display feedback form and user's feedback history
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        // Get current user
        $user = User::where('taikhoan', session('users'))->first();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user's feedback history
        $userFeedbacks = Feedback::where('uid', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        // Count unread messages (status = 1 means admin replied but user hasn't read)
        $unreadCount = Feedback::where('uid', $user->id)
            ->where('status', 1)
            ->whereNotNull('admin_reply')
            ->count();

        return view('pages.feedback', compact('user', 'userFeedbacks', 'unreadCount'));
    }

    /**
     * Store new feedback
     */
    public function store(Request $request)
    {
        // Check if user is logged in
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        // Get current user
        $user = User::where('taikhoan', session('users'))->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin tài khoản!');
        }

        // Validate input
        $request->validate([
            'email' => 'required|email',
            'message' => 'required|string|min:10'
        ], [
            'email.required' => 'Vui lòng nhập email liên hệ!',
            'email.email' => 'Email không hợp lệ!',
            'message.required' => 'Vui lòng nhập nội dung phản hồi!',
            'message.min' => 'Nội dung phản hồi phải có ít nhất 10 ký tự!'
        ]);

        // Create feedback
        $time = date('d/m/Y - H:i:s');
        $feedback = Feedback::create([
            'uid' => $user->id,
            'username' => $user->taikhoan,
            'email' => $request->email,
            'message' => $request->message,
            'status' => 0, // 0 = pending
            'time' => $time
        ]);

        // Send Telegram notification to admin
        $this->telegramService->notifyNewFeedback([
            'username' => $user->taikhoan,
            'title' => 'Phản hồi từ khách hàng',
            'content' => $request->message,
            'time' => $time
        ]);

        return redirect()->route('feedback.index')
            ->with('success', 'Phản hồi của bạn đã được gửi! Chúng tôi sẽ xem xét và phản hồi sớm nhất có thể.');
    }
}
