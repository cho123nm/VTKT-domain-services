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

        // Nếu không phải lệnh, thông báo bot chỉ dùng để admin nhận thông báo
        $adminChatId = config('services.telegram.admin_chat_id');
        $settings = \App\Models\Settings::getOne();
        if ($settings && !empty($settings->telegram_admin_chat_id)) {
            $adminChatId = $settings->telegram_admin_chat_id;
        }
        
        // Chỉ admin mới có thể tương tác, user khác chỉ nhận thông báo
        if ($chatId != $adminChatId) {
            $message = "ℹ️ Bot này chỉ dùng để Admin nhận thông báo.\n\n" .
                       "Để gửi phản hồi, vui lòng sử dụng form trên website:\n" .
                       "https://vtkt.online/feedback";
            $this->telegramService->sendMessage($chatId, $message);
            return;
        }
        
        // Nếu là admin nhưng gửi tin nhắn không phải lệnh, không xử lý
        Log::info('Admin sent non-command message', ['chat_id' => $chatId, 'text' => $text]);
    }

    /**
     * Xử lý lệnh /start từ Telegram
     * Thông báo bot chỉ dùng để admin nhận thông báo
     * 
     * @param string $chatId - ID chat để gửi tin nhắn
     * @return void
     */
    protected function handleStartCommand(string $chatId): void
    {
        // Kiểm tra xem có phải admin không (so sánh với admin chat ID)
        $adminChatId = config('services.telegram.admin_chat_id');
        $settings = \App\Models\Settings::getOne();
        if ($settings && !empty($settings->telegram_admin_chat_id)) {
            $adminChatId = $settings->telegram_admin_chat_id;
        }
        
        // Nếu là admin, gửi thông báo khác
        if ($chatId == $adminChatId) {
            $message = "👋 Chào mừng Admin!\n\n" .
                       "Bot này dùng để nhận thông báo về:\n" .
                       "• Feedback mới từ khách hàng\n" .
                       "• Đơn hàng mới\n\n" .
                       "Bạn sẽ nhận được thông báo tự động khi có feedback hoặc đơn hàng mới.";
        } else {
            // Nếu không phải admin, thông báo bot chỉ dùng để admin nhận thông báo
            $message = "ℹ️ <b>Thông báo</b>\n\n" .
                       "Bot này chỉ dùng để Admin nhận thông báo về feedback và đơn hàng.\n\n" .
                       "Để gửi phản hồi, vui lòng sử dụng form trên website:\n" .
                       "https://vtkt.online/feedback";

            // Gửi tin nhắn qua TelegramService
            $this->telegramService->sendMessage($chatId, $message);
            return;
        }

        // Gửi tin nhắn qua TelegramService
        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Xử lý lệnh /help từ Telegram
     * Gửi hướng dẫn
     * 
     * @param string $chatId - ID chat để gửi tin nhắn
     * @return void
     */
    protected function handleHelpCommand(string $chatId): void
    {
        // Kiểm tra xem có phải admin không
        $adminChatId = config('services.telegram.admin_chat_id');
        $settings = \App\Models\Settings::getOne();
        if ($settings && !empty($settings->telegram_admin_chat_id)) {
            $adminChatId = $settings->telegram_admin_chat_id;
        }
        
        if ($chatId == $adminChatId) {
            $message = "📋 <b>HƯỚNG DẪN CHO ADMIN</b>\n\n" .
                       "Bot này tự động gửi thông báo về:\n" .
                       "• Feedback mới từ khách hàng\n" .
                       "• Đơn hàng mới\n\n" .
                       "Khi nhận được thông báo feedback, bạn có thể:\n" .
                       "• Click nút '✅ Đã hỗ trợ' để đánh dấu đã xử lý\n" .
                       "• Xem chi tiết trên Admin Panel";
        } else {
            $message = "📋 <b>HƯỚNG DẪN</b>\n\n" .
                       "Bot này chỉ dùng để Admin nhận thông báo.\n\n" .
                       "Để gửi phản hồi, vui lòng:\n" .
                       "1. Truy cập: https://vtkt.online/feedback\n" .
                       "2. Điền form phản hồi\n" .
                       "3. Gửi phản hồi";
        }

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
     * Xử lý callback query từ Telegram
     * Callback query được gửi khi admin click vào button inline (ví dụ: "Đã hỗ trợ")
     * 
     * @param array $callbackQuery - Mảng chứa thông tin callback query từ Telegram
     * @return void
     */
    protected function processCallbackQuery(array $callbackQuery): void
    {
        // Lấy thông tin từ callback query
        $callbackQueryId = $callbackQuery['id'] ?? null; // ID callback query (dùng để answer)
        $from = $callbackQuery['from'] ?? []; // Thông tin người click
        $chatId = $from['id'] ?? null; // Chat ID của người click
        $data = $callbackQuery['data'] ?? ''; // Data từ button (ví dụ: feedback_done_123)
        $message = $callbackQuery['message'] ?? []; // Tin nhắn gốc chứa button

        // Ghi log callback query
        Log::info('Telegram callback query received', [
            'chat_id' => $chatId,
            'data' => $data,
            'callback_query_id' => $callbackQueryId
        ]);

        // Kiểm tra xem có phải admin không
        $adminChatId = config('services.telegram.admin_chat_id');
        $settings = \App\Models\Settings::getOne();
        if ($settings && !empty($settings->telegram_admin_chat_id)) {
            $adminChatId = $settings->telegram_admin_chat_id;
        }

        if ($chatId != $adminChatId) {
            // Nếu không phải admin, trả lời lỗi
            $this->telegramService->answerCallbackQuery($callbackQueryId, 'Chỉ admin mới có thể thực hiện hành động này.');
            return;
        }

        // Xử lý callback "Đã hỗ trợ" feedback
        if (strpos($data, 'feedback_done_') === 0) {
            $feedbackId = str_replace('feedback_done_', '', $data);
            
            // Cập nhật status feedback trong database
            try {
                $feedback = \App\Models\Feedback::find($feedbackId);
                if ($feedback) {
                    $feedback->status = 1; // Đánh dấu đã xử lý
                    $feedback->reply_time = date('d/m/Y - H:i:s'); // Thời gian xử lý
                    $feedback->save();

                    // Trả lời callback query thành công
                    $this->telegramService->answerCallbackQuery($callbackQueryId, '✅ Đã đánh dấu feedback #' . $feedbackId . ' là đã hỗ trợ!');
                    
                    // Cập nhật tin nhắn để hiển thị đã xử lý
                    $messageId = $message['message_id'] ?? null;
                    if ($messageId) {
                        $updatedText = $message['text'] ?? '';
                        $updatedText .= "\n\n✅ <b>Đã xử lý</b> - " . date('d/m/Y H:i:s');
                        
                        // Cập nhật tin nhắn (xóa button)
                        $this->telegramService->editMessageText(
                            $chatId,
                            $messageId,
                            $updatedText
                        );
                    }

                    Log::info('Feedback marked as done', [
                        'feedback_id' => $feedbackId,
                        'admin_chat_id' => $chatId
                    ]);
                } else {
                    $this->telegramService->answerCallbackQuery($callbackQueryId, '❌ Không tìm thấy feedback này.');
                }
            } catch (\Exception $e) {
                Log::error('Error processing feedback callback', [
                    'error' => $e->getMessage(),
                    'feedback_id' => $feedbackId
                ]);
                $this->telegramService->answerCallbackQuery($callbackQueryId, '❌ Có lỗi xảy ra khi xử lý.');
            }
        } else {
            // Callback không được nhận diện
            $this->telegramService->answerCallbackQuery($callbackQueryId, 'Hành động không hợp lệ.');
        }
    }
}
