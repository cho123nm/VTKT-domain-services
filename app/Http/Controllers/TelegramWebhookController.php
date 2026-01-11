<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model và Service cần thiết
use App\Models\User; // Model quản lý người dùng
use App\Models\Feedback; // Model quản lý feedback
use App\Services\TelegramService; // Service gửi thông báo Telegram
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Log; // Facade để ghi log

/**
 * Class TelegramWebhookController
 * Controller xử lý webhook từ Telegram Bot
 * Nhận và xử lý các tin nhắn từ Telegram để tạo feedback
 */
class TelegramWebhookController extends Controller
{
    // Thuộc tính lưu trữ instance của TelegramService
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
     * Xử lý webhook đến từ Telegram
     * Nhận dữ liệu từ Telegram và xử lý message hoặc callback query
     * 
     * @param Request $request - HTTP request chứa dữ liệu webhook từ Telegram
     * @return \Illuminate\Http\Response - Response HTTP (200 OK hoặc lỗi)
     */
    public function handle(Request $request)
    {
        try {
            // Ghi log dữ liệu webhook để debug
            Log::info('Telegram webhook received', [
                'data' => $request->all() // Tất cả dữ liệu từ request
            ]);

            // Lấy tất cả dữ liệu từ request
            $update = $request->all();

            // Kiểm tra dữ liệu không được rỗng
            if (empty($update)) {
                Log::warning('Telegram webhook: Empty update data');
                return response('Invalid request', 400); // HTTP 400 Bad Request
            }

            // Lấy message và callback_query từ update
            $message = $update['message'] ?? null; // Tin nhắn từ user
            $callbackQuery = $update['callback_query'] ?? null; // Callback query (cho button inline)

            // Xử lý tin nhắn nếu có
            if ($message) {
                $this->processMessage($message);
            }

            // Xử lý callback query nếu có (dùng trong tương lai)
            if ($callbackQuery) {
                $this->processCallbackQuery($callbackQuery);
            }

            // Trả về HTTP 200 OK để Telegram biết đã nhận được webhook
            return response('OK', 200);
        } catch (\Exception $e) {
            // Ghi log lỗi nếu có exception
            Log::error('Telegram webhook error', [
                'message' => $e->getMessage(), // Thông báo lỗi
                'trace' => $e->getTraceAsString() // Stack trace
            ]);

            // Trả về HTTP 500 Internal Server Error
            return response('Error', 500);
        }
    }

    /**
     * Xử lý tin nhắn đến từ Telegram
     * Phân tích tin nhắn và xử lý các lệnh (/start, /help) hoặc tạo feedback
     * 
     * @param array $message - Mảng chứa thông tin tin nhắn từ Telegram
     * @return void
     */
    protected function processMessage(array $message): void
    {
        // Lấy thông tin từ message
        $chatId = $message['chat']['id'] ?? null; // ID chat (dùng để gửi tin nhắn lại)
        $text = $message['text'] ?? ''; // Nội dung tin nhắn
        $from = $message['from'] ?? []; // Thông tin người gửi
        $username = $from['username'] ?? $from['first_name'] ?? 'Unknown'; // Username hoặc tên

        // Kiểm tra chat ID có tồn tại không
        if (!$chatId) {
            Log::warning('Telegram message: Missing chat ID');
            return; // Thoát nếu không có chat ID
        }

        // Ghi log tin nhắn đã nhận
        Log::info('Telegram message received', [
            'chat_id' => $chatId, // ID chat
            'username' => $username, // Username
            'text' => $text // Nội dung tin nhắn
        ]);

        // Xử lý lệnh /start
        if (strpos($text, '/start') === 0) {
            $this->handleStartCommand($chatId);
            return;
        }

        // Xử lý lệnh /help
        if (strpos($text, '/help') === 0) {
            $this->handleHelpCommand($chatId);
            return;
        }

        // Xử lý tin nhắn feedback (không phải lệnh)
        $this->processFeedbackMessage($chatId, $text, $from);
    }

    /**
     * Xử lý lệnh /start từ Telegram
     * Gửi thông báo chào mừng và hướng dẫn gửi feedback
     * 
     * @param string $chatId - ID chat để gửi tin nhắn
     * @return void
     */
    protected function handleStartCommand(string $chatId): void
    {
        // Tạo nội dung tin nhắn chào mừng và hướng dẫn
        $message = "👋 Chào mừng bạn đến với hệ thống hỗ trợ!\n\n" .
                   "Để gửi phản hồi, vui lòng nhập theo format:\n\n" .
                   "📧 Email của bạn\n\n" .
                   "❌ Mô tả lỗi/phản hồi\n\n" .
                   "Ví dụ:\n" .
                   "email@example.com\n\n" .
                   "Tôi gặp lỗi khi thanh toán";

        // Gửi tin nhắn qua TelegramService
        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Xử lý lệnh /help từ Telegram
     * Gửi hướng dẫn chi tiết cách gửi feedback
     * 
     * @param string $chatId - ID chat để gửi tin nhắn
     * @return void
     */
    protected function handleHelpCommand(string $chatId): void
    {
        // Tạo nội dung tin nhắn hướng dẫn
        $message = "📋 HƯỚNG DẪN GỬI PHẢN HỒI\n\n" .
                   "1. Nhập email của bạn\n" .
                   "2. Nhấn Enter\n" .
                   "3. Nhập mô tả lỗi/phản hồi\n\n" .
                   "Hoặc gửi theo format:\n\n" .
                   "📧 Email\n\n" .
                   "❌ Mô tả lỗi";

        // Gửi tin nhắn qua TelegramService
        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Xử lý tin nhắn feedback từ user
     * Phân tích email và nội dung feedback, sau đó lưu vào database
     * 
     * @param string $chatId - ID chat để gửi tin nhắn phản hồi
     * @param string $text - Nội dung tin nhắn từ user
     * @param array $from - Thông tin người gửi (username, first_name, etc.)
     * @return void
     */
    protected function processFeedbackMessage(string $chatId, string $text, array $from): void
    {
        // Phân tích email và nội dung feedback từ tin nhắn
        $lines = explode("\n", $text); // Tách tin nhắn thành các dòng
        $email = ''; // Email được tìm thấy
        $feedbackMessage = ''; // Nội dung feedback

        // Tìm email trong tin nhắn
        foreach ($lines as $line) {
            $line = trim($line); // Loại bỏ khoảng trắng đầu cuối
            // Kiểm tra dòng có phải là email hợp lệ không
            if (filter_var($line, FILTER_VALIDATE_EMAIL)) {
                $email = $line; // Lưu email
                break; // Dừng khi tìm thấy email đầu tiên
            }
        }

        // Nếu không tìm thấy email hợp lệ, dùng dòng đầu tiên làm email (có thể không đúng format)
        if (empty($email) && !empty($lines[0])) {
            $email = trim($lines[0]);
        }

        // Lấy nội dung feedback (loại trừ dòng email)
        $feedbackLines = []; // Mảng chứa các dòng feedback
        $foundEmail = false; // Biến đánh dấu đã tìm thấy email chưa
        foreach ($lines as $line) {
            $line = trim($line);
            // Nếu dòng này là email, đánh dấu đã tìm thấy email và bỏ qua dòng này
            if (filter_var($line, FILTER_VALIDATE_EMAIL)) {
                $foundEmail = true;
                continue; // Bỏ qua dòng email
            }
            // Nếu đã tìm thấy email hoặc dòng không rỗng, thêm vào feedback
            if ($foundEmail || !empty($line)) {
                $feedbackLines[] = $line;
            }
        }

        // Ghép các dòng feedback thành một chuỗi
        $feedbackMessage = implode("\n", $feedbackLines);

        // Nếu không có nội dung feedback, dùng toàn bộ tin nhắn làm feedback
        if (empty($feedbackMessage)) {
            $feedbackMessage = $text;
        }

        // Tìm user trong database theo email
        $user = null; // User được tìm thấy
        $userId = 0; // ID user (mặc định: 0)
        $username = $from['first_name'] ?? $from['username'] ?? 'Unknown'; // Username từ Telegram

        // Nếu có email hợp lệ, tìm user trong database
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $email)->first(); // Tìm user theo email
            if ($user) {
                $userId = $user->id; // Lấy ID user
                $username = $user->taikhoan; // Lấy username từ database
            }
        }

        // Nếu không tìm thấy user, dùng username từ Telegram làm email
        if (!$user) {
            $email = $email ?: ($from['username'] ?? '') . '@telegram';
        }

        // Lưu feedback vào database
        try {
            // Tạo chuỗi thời gian định dạng Việt Nam
            $time = date('d/m/Y - H:i:s');

            // Tạo feedback mới trong database
            $feedback = Feedback::create([
                'uid' => $userId, // ID user (0 nếu không tìm thấy)
                'username' => $username, // Username
                'email' => $email, // Email
                'message' => $feedbackMessage, // Nội dung feedback
                'telegram_chat_id' => (string)$chatId, // Chat ID từ Telegram (ép kiểu về string)
                'time' => $time, // Thời gian tạo
                'status' => 0 // Trạng thái: 0 = Chưa đọc
            ]);

            // Gửi tin nhắn xác nhận cho user
            $confirmMessage = "✅ Phản hồi của bạn đã được gửi thành công!\n\n" .
                            "Chúng tôi sẽ xem xét và phản hồi sớm nhất có thể.\n\n" .
                            "📧 Email: " . $email;

            $this->telegramService->sendMessage($chatId, $confirmMessage);

            // Gửi thông báo cho admin về feedback mới
            $this->telegramService->notifyNewFeedback([
                'username' => $username, // Username
                'title' => 'Phản hồi từ Telegram', // Tiêu đề
                'content' => $feedbackMessage, // Nội dung feedback
                'time' => $time // Thời gian
            ]);

            // Ghi log feedback đã được lưu thành công
            Log::info('Telegram feedback saved', [
                'feedback_id' => $feedback->id, // ID feedback
                'user_id' => $userId, // ID user
                'email' => $email // Email
            ]);
        } catch (\Exception $e) {
            // Ghi log lỗi nếu không lưu được feedback
            Log::error('Failed to save Telegram feedback', [
                'error' => $e->getMessage(), // Thông báo lỗi
                'chat_id' => $chatId // Chat ID
            ]);

            // Gửi tin nhắn lỗi cho user
            $errorMessage = "❌ Có lỗi xảy ra khi gửi phản hồi. Vui lòng thử lại sau.";
            $this->telegramService->sendMessage($chatId, $errorMessage);
        }
    }

    /**
     * Xử lý callback query từ Telegram (dùng trong tương lai)
     * Callback query được gửi khi user click vào button inline
     * 
     * @param array $callbackQuery - Mảng chứa thông tin callback query từ Telegram
     * @return void
     */
    protected function processCallbackQuery(array $callbackQuery): void
    {
        // Xử lý callback query nếu cần trong tương lai
        // Hiện tại chỉ ghi log để theo dõi
        Log::info('Telegram callback query received', [
            'data' => $callbackQuery // Dữ liệu callback query
        ]);
    }
}
