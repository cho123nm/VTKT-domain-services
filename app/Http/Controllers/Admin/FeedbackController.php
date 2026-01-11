<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model cần thiết
use App\Models\Feedback; // Model quản lý feedback
use App\Models\Settings; // Model quản lý cài đặt hệ thống
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class FeedbackController
 * Controller xử lý quản lý feedback trong admin panel
 */
class FeedbackController extends Controller
{
    /**
     * Hiển thị danh sách feedback
     * Lấy tất cả feedback và đếm số feedback đang chờ xử lý
     * 
     * @return \Illuminate\View\View - View danh sách feedback
     */
    public function index()
    {
        // Lấy tất cả feedback từ database, sắp xếp theo ID giảm dần (mới nhất trước)
        $feedbacks = Feedback::orderBy('id', 'desc')->get();
        // Đếm số feedback đang chờ xử lý (status = 0)
        $pendingCount = Feedback::where('status', 0)->count();
        
        // Trả về view với dữ liệu feedback và số lượng đang chờ xử lý
        return view('admin.feedback.index', compact('feedbacks', 'pendingCount'));
    }

    /**
     * Hiển thị chi tiết feedback
     * 
     * @param int $id - ID của feedback cần xem chi tiết
     * @return \Illuminate\View\View - View chi tiết feedback
     */
    public function show($id)
    {
        // Tìm feedback theo ID, nếu không tìm thấy thì throw 404
        $feedback = Feedback::findOrFail($id);
        
        // Trả về view với dữ liệu feedback
        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Trả lời feedback từ admin
     * Lưu phản hồi của admin và gửi thông báo qua Telegram nếu có chat ID
     * 
     * @param Request $request - HTTP request chứa admin_reply
     * @param int $id - ID của feedback cần trả lời
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách feedback với thông báo
     */
    public function reply(Request $request, $id)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'admin_reply' => 'required|string', // Phản hồi của admin bắt buộc
        ]);

        // Tìm feedback theo ID, nếu không tìm thấy thì throw 404
        $feedback = Feedback::findOrFail($id);
        // Tạo chuỗi thời gian định dạng Việt Nam
        $replyTime = date('d/m/Y - H:i:s');
        
        // Cập nhật phản hồi của admin và thời gian phản hồi
        $feedback->admin_reply = $request->admin_reply; // Phản hồi của admin
        $feedback->reply_time = $replyTime; // Thời gian phản hồi
        $feedback->status = 1; // Trạng thái: 1 = Đã trả lời
        $feedback->save(); // Lưu vào database

        // Gửi tin nhắn qua Telegram nếu có chat ID
        $telegramSent = false; // Biến để đánh dấu đã gửi Telegram thành công chưa
        if (!empty($feedback->telegram_chat_id)) {
            // Lấy cài đặt Telegram từ database
            $settings = Settings::first();
            $telegramBotToken = $settings->telegram_bot_token ?? '';
            
            // Nếu có bot token, gửi tin nhắn qua Telegram
            if (!empty($telegramBotToken)) {
                // Tạo nội dung tin nhắn
                $message = "✅ PHẢN HỒI TỪ ADMIN\n\n";
                $message .= $request->admin_reply . "\n\n";
                $message .= "⏰ " . $replyTime;
                
                // Gửi tin nhắn qua Telegram
                $telegramSent = $this->sendTelegramMessage($telegramBotToken, $feedback->telegram_chat_id, $message);
            }
        }

        // Tạo thông báo thành công
        $successMsg = "Đã gửi phản hồi thành công!";
        if ($telegramSent) {
            $successMsg .= " Tin nhắn đã được gửi qua Telegram.";
        } elseif (!empty($feedback->telegram_chat_id)) {
            $successMsg .= " Lưu ý: Không thể gửi qua Telegram.";
        }

        // Redirect về danh sách feedback với thông báo thành công
        return redirect()->route('admin.feedback.index')
            ->with('success', $successMsg);
    }

    /**
     * Cập nhật trạng thái feedback (đánh dấu đã đọc)
     * 
     * @param Request $request - HTTP request chứa status
     * @param int $id - ID của feedback cần cập nhật trạng thái
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách feedback với thông báo
     */
    public function updateStatus(Request $request, $id)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'status' => 'required|integer|in:0,1,2', // Trạng thái bắt buộc, chỉ nhận 0, 1, 2
        ]);

        // Tìm feedback theo ID, nếu không tìm thấy thì throw 404
        $feedback = Feedback::findOrFail($id);
        // Cập nhật trạng thái
        $feedback->status = $request->status;
        $feedback->save(); // Lưu vào database

        // Redirect về danh sách feedback với thông báo thành công
        return redirect()->route('admin.feedback.index')
            ->with('success', 'Đã cập nhật trạng thái thành công!');
    }

    /**
     * Gửi tin nhắn qua Telegram
     * Private method - chỉ được gọi từ trong class này
     * 
     * @param string $token - Telegram bot token
     * @param string $chatId - Telegram chat ID của người nhận
     * @param string $message - Nội dung tin nhắn
     * @return bool - True nếu gửi thành công (HTTP 200), False nếu không
     */
    private function sendTelegramMessage(string $token, string $chatId, string $message): bool
    {
        // Kiểm tra token và chatId không được rỗng
        if (empty($token) || empty($chatId)) {
            return false;
        }
        
        // Tạo URL API Telegram để gửi tin nhắn
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        
        // Tạo dữ liệu POST
        $data = [
            'chat_id' => $chatId, // ID chat đích
            'text' => $message, // Nội dung tin nhắn
            'parse_mode' => 'HTML' // Chế độ parse: HTML
        ];
        
        // Khởi tạo cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // Thiết lập URL
        curl_setopt($ch, CURLOPT_POST, true); // Thiết lập POST request
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Thiết lập dữ liệu POST
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Trả về response dưới dạng string
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Đặt timeout 10 giây
        
        // Thực thi request và lấy response
        $response = curl_exec($ch);
        // Lấy HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Đóng cURL
        curl_close($ch);
        
        // Trả về true nếu HTTP status code là 200 (thành công)
        return $httpCode === 200;
    }
}

