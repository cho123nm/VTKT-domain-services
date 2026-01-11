<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model cần thiết
use App\Models\Feedback; // Model quản lý feedback
use App\Models\User; // Model quản lý người dùng
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class MessageController
 * Controller xử lý tin nhắn/phản hồi từ admin cho user
 */
class MessageController extends Controller
{
    /**
     * Hiển thị lịch sử tin nhắn/phản hồi từ admin
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

        // Lấy lịch sử feedback của user (bao gồm cả phản hồi từ admin)
        $userFeedbacks = Feedback::where('uid', $user->id)
            ->orderBy('id', 'desc') // Sắp xếp theo ID giảm dần (mới nhất trước)
            ->get();

        // Đếm số tin nhắn chưa đọc (status = 1 nghĩa là admin đã phản hồi nhưng user chưa đọc)
        $unreadCount = Feedback::where('uid', $user->id)
            ->where('status', 1) // Đã được admin phản hồi
            ->whereNotNull('admin_reply') // Có phản hồi từ admin
            ->count();

        // Trả về view với dữ liệu user, lịch sử feedback và số tin nhắn chưa đọc
        return view('pages.messages', compact('user', 'userFeedbacks', 'unreadCount'));
    }

    /**
     * Đánh dấu feedback là đã đọc
     * 
     * @param int $id - ID của feedback cần đánh dấu đã đọc
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead($id)
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

        // Tìm feedback và kiểm tra quyền sở hữu (đảm bảo user chỉ đánh dấu feedback của mình)
        $feedback = Feedback::where('id', $id)
            ->where('uid', $user->id) // Chỉ tìm feedback của user này
            ->first();

        // Nếu tìm thấy feedback, cập nhật trạng thái thành đã đọc
        if ($feedback) {
            // Cập nhật status = 2 (đã đọc)
            $feedback->status = 2;
            $feedback->save();
        }

        // Redirect về trang messages
        return redirect()->route('messages.index');
    }
}
