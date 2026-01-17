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
        
        // Nếu là admin, kiểm tra các lệnh đặc biệt
        if ($chatId == $adminChatId) {
            // Xử lý lệnh cộng tiền: congtien:username:amount
            if (preg_match('/^congtien:([^:]+):(\d+)$/i', $text, $matches)) {
                $this->processAddBalance($chatId, $matches[1], $matches[2]);
                return;
            }
            
            // Xử lý lệnh cập nhật DNS: updatedns:domain:ns1:ns2
            if (preg_match('/^updatedns:([^:]+):([^:]+):([^:]+)$/i', $text, $matches)) {
                $this->processUpdateDNS($chatId, $matches[1], $matches[2], $matches[3]);
                return;
            }
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
        
        // Nếu là admin, hiển thị menu chính
        if ($chatId == $adminChatId) {
            $message = "👋 <b>CHÀO MỪNG ADMIN!</b>\n\n" .
                       "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
                       "📱 <b>MENU QUẢN LÝ</b>\n" .
                       "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n" .
                       "Chọn chức năng bạn muốn sử dụng:";
            
            // Tạo menu với inline keyboard - nút to hơn, đẹp hơn
            $menuKeyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '📋 FEEDBACK CHỜ XỬ LÝ', 'callback_data' => 'menu_pending_feedback']
                    ],
                    [
                        ['text' => '✅ FEEDBACK ĐÃ XỬ LÝ', 'callback_data' => 'menu_processed_feedback']
                    ],
                    [
                        ['text' => '📊 THỐNG KÊ TÀI KHOẢN', 'callback_data' => 'menu_user_stats']
                    ],
                    [
                        ['text' => '💰 CỘNG TIỀN CHO TK', 'callback_data' => 'menu_add_balance']
                    ],
                    [
                        ['text' => '🌐 CẬP NHẬT DNS', 'callback_data' => 'menu_update_dns']
                    ],
                    [
                        ['text' => '📦 ĐƠN HÀNG MỚI', 'callback_data' => 'menu_new_orders']
                    ],
                    [
                        ['text' => 'ℹ️ TRỢ GIÚP', 'callback_data' => 'menu_help']
                    ]
                ]
            ];
            
            $this->telegramService->sendMessage($chatId, $message, 'HTML', $menuKeyboard);
            return;
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

        // Xử lý các menu item
        if ($data === 'menu_pending_feedback') {
            $this->handlePendingFeedback($chatId, $callbackQueryId, $message);
            return;
        } elseif ($data === 'menu_processed_feedback') {
            $this->handleProcessedFeedback($chatId, $callbackQueryId, $message);
            return;
        } elseif ($data === 'menu_user_stats' || strpos($data, 'user_stats_page_') === 0) {
            $this->handleUserStats($chatId, $callbackQueryId, $message);
            return;
        } elseif ($data === 'menu_add_balance' || strpos($data, 'add_balance_user_') === 0 || strpos($data, 'add_balance_amount_') === 0) {
            $this->handleAddBalance($chatId, $callbackQueryId, $message, $data);
            return;
        } elseif ($data === 'menu_update_dns' || strpos($data, 'update_dns_') === 0) {
            $this->handleUpdateDNS($chatId, $callbackQueryId, $message, $data);
            return;
        } elseif ($data === 'menu_new_orders') {
            $this->handleNewOrders($chatId, $callbackQueryId, $message);
            return;
        } elseif ($data === 'menu_help') {
            $this->handleHelpCommand($chatId);
            $this->telegramService->answerCallbackQuery($callbackQueryId, 'Đã hiển thị hướng dẫn');
            return;
        } elseif ($data === 'menu_back') {
            // Quay về menu chính
            $this->handleStartCommand($chatId);
            $this->telegramService->answerCallbackQuery($callbackQueryId, 'Đã quay về menu chính');
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

    /**
     * Xử lý xem feedback chờ xử lý
     */
    protected function handlePendingFeedback(string $chatId, ?string $callbackQueryId, array $message): void
    {
        try {
            $feedbacks = \App\Models\Feedback::where('status', 0)
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            if ($feedbacks->isEmpty()) {
                $text = "✅ <b>KHÔNG CÓ FEEDBACK CHỜ XỬ LÝ</b>\n\nTất cả feedback đã được xử lý!";
            } else {
                $text = "📋 <b>FEEDBACK CHỜ XỬ LÝ</b> (" . $feedbacks->count() . ")\n\n";
                foreach ($feedbacks as $feedback) {
                    $text .= "🆔 <b>#{$feedback->id}</b>\n";
                    $text .= "👤 <code>{$feedback->username}</code>\n";
                    $text .= "📧 <code>{$feedback->email}</code>\n";
                    $content = mb_substr($feedback->message, 0, 100);
                    if (mb_strlen($feedback->message) > 100) $content .= '...';
                    $text .= "📝 {$content}\n";
                    $text .= "⏰ {$feedback->time}\n\n";
                }
            }

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🔄 Làm mới', 'callback_data' => 'menu_pending_feedback']],
                    [['text' => '🏠 Về menu chính', 'callback_data' => 'menu_back']]
                ]
            ];

            $messageId = $message['message_id'] ?? null;
            if ($messageId) {
                $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
            } else {
                $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            }
            if ($callbackQueryId) {
                $this->telegramService->answerCallbackQuery($callbackQueryId, 'Đã tải danh sách feedback');
            }
        } catch (\Exception $e) {
            Log::error('Error handling pending feedback', ['error' => $e->getMessage()]);
            if ($callbackQueryId) {
                $this->telegramService->answerCallbackQuery($callbackQueryId, '❌ Có lỗi xảy ra');
            }
        }
    }

    /**
     * Xử lý xem feedback đã xử lý
     */
    protected function handleProcessedFeedback(string $chatId, ?string $callbackQueryId, array $message): void
    {
        try {
            $feedbacks = \App\Models\Feedback::where('status', 1)
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            if ($feedbacks->isEmpty()) {
                $text = "📭 <b>CHƯA CÓ FEEDBACK NÀO ĐÃ XỬ LÝ</b>";
            } else {
                $text = "✅ <b>FEEDBACK ĐÃ XỬ LÝ</b> (" . $feedbacks->count() . ")\n\n";
                foreach ($feedbacks as $feedback) {
                    $text .= "🆔 <b>#{$feedback->id}</b>\n";
                    $text .= "👤 <code>{$feedback->username}</code>\n";
                    $text .= "⏰ Xử lý: {$feedback->reply_time}\n\n";
                }
            }

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🔄 Làm mới', 'callback_data' => 'menu_processed_feedback']],
                    [['text' => '🏠 Về menu chính', 'callback_data' => 'menu_back']]
                ]
            ];

            $messageId = $message['message_id'] ?? null;
            if ($messageId) {
                $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
            } else {
                $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            }
            if ($callbackQueryId) {
                $this->telegramService->answerCallbackQuery($callbackQueryId, 'Đã tải danh sách feedback');
            }
        } catch (\Exception $e) {
            Log::error('Error handling processed feedback', ['error' => $e->getMessage()]);
            if ($callbackQueryId) {
                $this->telegramService->answerCallbackQuery($callbackQueryId, '❌ Có lỗi xảy ra');
            }
        }
    }

    /**
     * Xử lý thống kê tài khoản - hiển thị chi tiết từng tài khoản
     */
    protected function handleUserStats(string $chatId, ?string $callbackQueryId, array $message): void
    {
        try {
            $page = isset($message['text']) && preg_match('/page_(\d+)/', $message['text'], $matches) ? (int)$matches[1] : 1;
            $perPage = 5;
            $offset = ($page - 1) * $perPage;

            $totalUsers = \App\Models\User::count();
            $totalBalance = \App\Models\User::sum('tien');
            $activeUsers = \App\Models\User::where('tien', '>', 0)->count();
            $pendingFeedback = \App\Models\Feedback::where('status', 0)->count();

            $users = \App\Models\User::orderBy('id', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            $text = "📊 <b>THỐNG KÊ HỆ THỐNG</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $text .= "📈 <b>TỔNG QUAN</b>\n";
            $text .= "👥 Tổng TK: <b>{$totalUsers}</b>\n";
            $text .= "💰 Tổng dư: <b>" . number_format($totalBalance, 0, ',', '.') . " VNĐ</b>\n";
            $text .= "✅ TK có dư: <b>{$activeUsers}</b>\n";
            $text .= "📋 Feedback chờ: <b>{$pendingFeedback}</b>\n\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            $text .= "👤 <b>CHI TIẾT TÀI KHOẢN</b> (Trang {$page}/" . ceil($totalUsers / $perPage) . ")\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

            if ($users->isEmpty()) {
                $text .= "Không có tài khoản nào.";
            } else {
                foreach ($users as $user) {
                    $text .= "🆔 <b>ID:</b> {$user->id}\n";
                    $text .= "👤 <b>TK:</b> <code>{$user->taikhoan}</code>\n";
                    $text .= "📧 <b>Email:</b> <code>{$user->email}</code>\n";
                    $text .= "💰 <b>Số dư:</b> <b>" . number_format($user->tien, 0, ',', '.') . " VNĐ</b>\n";
                    $text .= "⏰ <b>Ngày tạo:</b> {$user->time}\n";
                    $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                }
            }

            $keyboard = ['inline_keyboard' => []];
            
            // Nút phân trang
            if ($page > 1) {
                $keyboard['inline_keyboard'][] = [['text' => '⬅️ Trước', 'callback_data' => 'user_stats_page_' . ($page - 1)]];
            }
            if ($page < ceil($totalUsers / $perPage)) {
                $keyboard['inline_keyboard'][] = [['text' => 'Tiếp ➡️', 'callback_data' => 'user_stats_page_' . ($page + 1)]];
            }
            
            $keyboard['inline_keyboard'][] = [
                ['text' => '🔄 Làm mới', 'callback_data' => 'menu_user_stats'],
                ['text' => '🏠 Menu', 'callback_data' => 'menu_back']
            ];

            $messageId = $message['message_id'] ?? null;
            if ($messageId) {
                $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
            } else {
                $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            }
            if ($callbackQueryId) {
                $this->telegramService->answerCallbackQuery($callbackQueryId, 'Đã tải thống kê');
            }
        } catch (\Exception $e) {
            Log::error('Error handling user stats', ['error' => $e->getMessage()]);
            if ($callbackQueryId) {
                $this->telegramService->answerCallbackQuery($callbackQueryId, '❌ Có lỗi xảy ra');
            }
        }
    }

    /**
     * Xử lý cộng tiền cho tài khoản - hiển thị danh sách tài khoản
     */
    protected function handleAddBalance(string $chatId, ?string $callbackQueryId, array $message, string $data = 'menu_add_balance'): void
    {
        try {
            // Nếu click vào user cụ thể
            if (strpos($data, 'add_balance_user_') === 0) {
                $userId = str_replace('add_balance_user_', '', $data);
                $user = \App\Models\User::find($userId);
                if (!$user) {
                    $this->telegramService->answerCallbackQuery($callbackQueryId, '❌ Không tìm thấy tài khoản');
                    return;
                }

                $text = "💰 <b>CỘNG TIỀN CHO TÀI KHOẢN</b>\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                $text .= "👤 <b>Tài khoản:</b> <code>{$user->taikhoan}</code>\n";
                $text .= "📧 <b>Email:</b> <code>{$user->email}</code>\n";
                $text .= "💰 <b>Số dư hiện tại:</b> <b>" . number_format($user->tien, 0, ',', '.') . " VNĐ</b>\n\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
                $text .= "Chọn số tiền muốn cộng:\n\n";

                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '➕ 10,000 VNĐ', 'callback_data' => 'add_balance_amount_' . $userId . '_10000'],
                            ['text' => '➕ 50,000 VNĐ', 'callback_data' => 'add_balance_amount_' . $userId . '_50000']
                        ],
                        [
                            ['text' => '➕ 100,000 VNĐ', 'callback_data' => 'add_balance_amount_' . $userId . '_100000'],
                            ['text' => '➕ 500,000 VNĐ', 'callback_data' => 'add_balance_amount_' . $userId . '_500000']
                        ],
                        [
                            ['text' => '➕ 1,000,000 VNĐ', 'callback_data' => 'add_balance_amount_' . $userId . '_1000000']
                        ],
                        [
                            ['text' => '⬅️ Quay lại', 'callback_data' => 'menu_add_balance']
                        ]
                    ]
                ];

                $messageId = $message['message_id'] ?? null;
                if ($messageId) {
                    $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
                } else {
                    $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
                }
                if ($callbackQueryId) {
                    $this->telegramService->answerCallbackQuery($callbackQueryId, 'Chọn số tiền');
                }
                return;
            }

            // Nếu click vào số tiền
            if (strpos($data, 'add_balance_amount_') === 0) {
                $parts = explode('_', $data);
                $userId = $parts[3];
                $amount = (int)$parts[4];
                
                $user = \App\Models\User::find($userId);
                if (!$user) {
                    $this->telegramService->answerCallbackQuery($callbackQueryId, '❌ Không tìm thấy tài khoản');
                    return;
                }

                $oldBalance = $user->tien;
                $user->incrementBalance($amount);
                $newBalance = $user->tien;

                $text = "✅ <b>CỘNG TIỀN THÀNH CÔNG!</b>\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                $text .= "👤 <b>Tài khoản:</b> <code>{$user->taikhoan}</code>\n";
                $text .= "💰 <b>Số tiền:</b> <b>" . number_format($amount, 0, ',', '.') . " VNĐ</b>\n";
                $text .= "📊 <b>Số dư cũ:</b> " . number_format($oldBalance, 0, ',', '.') . " VNĐ\n";
                $text .= "📊 <b>Số dư mới:</b> <b>" . number_format($newBalance, 0, ',', '.') . " VNĐ</b>\n";

                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '🔄 Cộng thêm', 'callback_data' => 'add_balance_user_' . $userId]],
                        [['text' => '🏠 Menu', 'callback_data' => 'menu_back']]
                    ]
                ];

                $messageId = $message['message_id'] ?? null;
                if ($messageId) {
                    $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
                } else {
                    $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
                }
                if ($callbackQueryId) {
                    $this->telegramService->answerCallbackQuery($callbackQueryId, '✅ Đã cộng tiền thành công!');
                }

                Log::info('Balance added via Telegram menu', [
                    'username' => $user->taikhoan,
                    'amount' => $amount,
                    'admin_chat_id' => $chatId
                ]);
                return;
            }

            // Hiển thị danh sách tài khoản
            $users = \App\Models\User::orderBy('id', 'desc')->limit(10)->get();

            $text = "💰 <b>CỘNG TIỀN CHO TÀI KHOẢN</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $text .= "Chọn tài khoản muốn cộng tiền:\n\n";

            $keyboard = ['inline_keyboard' => []];
            foreach ($users as $user) {
                $balance = number_format($user->tien, 0, ',', '.');
                $keyboard['inline_keyboard'][] = [
                    ['text' => "👤 {$user->taikhoan} (💰 {$balance} VNĐ)", 'callback_data' => 'add_balance_user_' . $user->id]
                ];
            }
            $keyboard['inline_keyboard'][] = [['text' => '🏠 Menu', 'callback_data' => 'menu_back']];

            $messageId = $message['message_id'] ?? null;
            if ($messageId) {
                $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
            } else {
                $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            }
            if ($callbackQueryId) {
                $this->telegramService->answerCallbackQuery($callbackQueryId, 'Đã tải danh sách tài khoản');
            }
        } catch (\Exception $e) {
            Log::error('Error handling add balance', ['error' => $e->getMessage()]);
            if ($callbackQueryId) {
                $this->telegramService->answerCallbackQuery($callbackQueryId, '❌ Có lỗi xảy ra');
            }
        }
    }

    /**
     * Xử lý cập nhật DNS - hiển thị danh sách domain đang yêu cầu
     */
    protected function handleUpdateDNS(string $chatId, ?string $callbackQueryId, array $message, string $data = 'menu_update_dns'): void
    {
        try {
            // Nếu click vào domain cụ thể để cập nhật
            if (strpos($data, 'update_dns_') === 0 && strpos($data, '_confirm_') === false) {
                $domainId = str_replace('update_dns_', '', $data);
                $history = \App\Models\History::find($domainId);
                if (!$history) {
                    $this->telegramService->answerCallbackQuery($callbackQueryId, '❌ Không tìm thấy domain');
                    return;
                }

                $username = $history->user ? $history->user->taikhoan : 'N/A';
                $domain = $history->domain;
                
                $text = "🌐 <b>CẬP NHẬT DNS</b>\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                $text .= "🌍 <b>Domain:</b> <code>" . $domain . "</code>\n";
                $text .= "👤 <b>User:</b> <code>" . $username . "</code>\n";
                $text .= "📊 <b>NS1 hiện tại:</b> <code>" . $history->ns1 . "</code>\n";
                $text .= "📊 <b>NS2 hiện tại:</b> <code>" . $history->ns2 . "</code>\n\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
                $text .= "Nhập DNS mới theo format:\n";
                $text .= "<code>updatedns:" . $domain . ":ns1:ns2</code>\n\n";
                $text .= "Ví dụ:\n";
                $text .= "<code>updatedns:" . $domain . ":ns1.example.com:ns2.example.com</code>";

                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '⬅️ Quay lại', 'callback_data' => 'menu_update_dns']]
                    ]
                ];

                $messageId = $message['message_id'] ?? null;
                if ($messageId) {
                    $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
                } else {
                    $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
                }
                if ($callbackQueryId) {
                    $this->telegramService->answerCallbackQuery($callbackQueryId, 'Nhập DNS mới');
                }
                return;
            }

            // Hiển thị danh sách domain đang yêu cầu cập nhật DNS (ahihi = 1)
            $domains = \App\Models\History::where('ahihi', '1')
                ->orderBy('id', 'desc')
                ->limit(20)
                ->get();

            $text = "🌐 <b>CẬP NHẬT DNS</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

            if ($domains->isEmpty()) {
                $text .= "✅ <b>KHÔNG CÓ ĐƠN NÀO YÊU CẦU CẬP NHẬT DNS</b>\n\n";
                $text .= "Tất cả đơn hàng đã được xử lý!";
            } else {
                $text .= "📋 <b>DANH SÁCH DOMAIN ĐANG YÊU CẦU</b> (" . $domains->count() . ")\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

                $keyboard = ['inline_keyboard' => []];
                foreach ($domains as $domain) {
                    $username = $domain->user->taikhoan ?? 'N/A';
                    $text .= "🌍 <b>Domain:</b> <code>{$domain->domain}</code>\n";
                    $text .= "👤 <b>User:</b> <code>{$username}</code>\n";
                    $text .= "📊 <b>NS1:</b> <code>{$domain->ns1}</code>\n";
                    $text .= "📊 <b>NS2:</b> <code>{$domain->ns2}</code>\n";
                    $text .= "⏰ <b>Thời gian:</b> {$domain->time}\n";
                    $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

                    $keyboard['inline_keyboard'][] = [
                        ['text' => "🌐 Cập nhật {$domain->domain}", 'callback_data' => 'update_dns_' . $domain->id]
                    ];
                }
                $keyboard['inline_keyboard'][] = [['text' => '🏠 Menu', 'callback_data' => 'menu_back']];
            }

            if (!isset($keyboard)) {
                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '🔄 Làm mới', 'callback_data' => 'menu_update_dns']],
                        [['text' => '🏠 Menu', 'callback_data' => 'menu_back']]
                    ]
                ];
            }

            $messageId = $message['message_id'] ?? null;
            if ($messageId) {
                $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
            } else {
                $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            }
            if ($callbackQueryId) {
                $this->telegramService->answerCallbackQuery($callbackQueryId, 'Đã tải danh sách');
            }
        } catch (\Exception $e) {
            Log::error('Error handling update DNS', ['error' => $e->getMessage()]);
            if ($callbackQueryId) {
                $this->telegramService->answerCallbackQuery($callbackQueryId, '❌ Có lỗi xảy ra');
            }
        }
    }

    /**
     * Xử lý xem đơn hàng mới - hiển thị trực quan
     */
    protected function handleNewOrders(string $chatId, ?string $callbackQueryId, array $message): void
    {
        try {
            // Đơn hàng mới (status = 0 - Chờ xử lý)
            $newOrders = \App\Models\History::where('status', 0)
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            // Đơn hàng đã duyệt (status = 1)
            $approvedOrders = \App\Models\History::where('status', 1)
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();

            $text = "📦 <b>QUẢN LÝ ĐƠN HÀNG</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

            // Thống kê
            $totalNew = \App\Models\History::where('status', 0)->count();
            $totalApproved = \App\Models\History::where('status', 1)->count();
            $totalCancelled = \App\Models\History::where('status', 2)->count();

            $text .= "📊 <b>THỐNG KÊ</b>\n";
            $text .= "⏳ Chờ xử lý: <b>{$totalNew}</b>\n";
            $text .= "✅ Đã duyệt: <b>{$totalApproved}</b>\n";
            $text .= "❌ Đã hủy: <b>{$totalCancelled}</b>\n\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

            if ($newOrders->isEmpty()) {
                $text .= "✅ <b>KHÔNG CÓ ĐƠN HÀNG MỚI</b>\n\n";
                $text .= "Tất cả đơn hàng đã được xử lý!";
            } else {
                $text .= "⏳ <b>ĐƠN HÀNG CHỜ XỬ LÝ</b> (" . $newOrders->count() . ")\n";
                $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

                foreach ($newOrders as $order) {
                    $username = $order->user->taikhoan ?? 'N/A';
                    $text .= "🆔 <b>ID:</b> {$order->id}\n";
                    $text .= "🌍 <b>Domain:</b> <code>{$order->domain}</code>\n";
                    $text .= "👤 <b>User:</b> <code>{$username}</code>\n";
                    $text .= "🔖 <b>MGD:</b> <code>{$order->mgd}</code>\n";
                    $text .= "📊 <b>NS1:</b> <code>{$order->ns1}</code>\n";
                    $text .= "📊 <b>NS2:</b> <code>{$order->ns2}</code>\n";
                    $text .= "⏰ <b>Thời gian:</b> {$order->time}\n";
                    $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
                }
            }

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🔄 Làm mới', 'callback_data' => 'menu_new_orders']],
                    [['text' => '🏠 Menu', 'callback_data' => 'menu_back']]
                ]
            ];

            $messageId = $message['message_id'] ?? null;
            if ($messageId) {
                $this->telegramService->editMessageText($chatId, $messageId, $text, 'HTML', $keyboard);
            } else {
                $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            }
            if ($callbackQueryId) {
                $this->telegramService->answerCallbackQuery($callbackQueryId, 'Đã tải danh sách đơn hàng');
            }
        } catch (\Exception $e) {
            Log::error('Error handling new orders', ['error' => $e->getMessage()]);
            if ($callbackQueryId) {
                $this->telegramService->answerCallbackQuery($callbackQueryId, '❌ Có lỗi xảy ra');
            }
        }
    }

    /**
     * Xử lý lệnh cộng tiền: congtien:username:amount
     */
    protected function processAddBalance(string $chatId, string $username, string $amount): void
    {
        try {
            $user = \App\Models\User::findByUsername($username);
            if (!$user) {
                $this->telegramService->sendMessage($chatId, "❌ Không tìm thấy tài khoản: <code>{$username}</code>", 'HTML');
                return;
            }

            $amountInt = (int)$amount;
            if ($amountInt <= 0) {
                $this->telegramService->sendMessage($chatId, "❌ Số tiền phải lớn hơn 0!", 'HTML');
                return;
            }

            $oldBalance = $user->tien;
            $user->incrementBalance($amountInt);
            $newBalance = $user->tien;

            $text = "✅ <b>CỘNG TIỀN THÀNH CÔNG</b>\n\n";
            $text .= "👤 Tài khoản: <code>{$username}</code>\n";
            $text .= "💰 Số tiền: <b>" . number_format($amountInt, 0, ',', '.') . " VNĐ</b>\n";
            $text .= "📊 Số dư cũ: <b>" . number_format($oldBalance, 0, ',', '.') . " VNĐ</b>\n";
            $text .= "📊 Số dư mới: <b>" . number_format($newBalance, 0, ',', '.') . " VNĐ</b>";

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🏠 Về menu chính', 'callback_data' => 'menu_back']]
                ]
            ];

            $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            
            Log::info('Balance added via Telegram', [
                'username' => $username,
                'amount' => $amountInt,
                'admin_chat_id' => $chatId
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing add balance', ['error' => $e->getMessage()]);
            $this->telegramService->sendMessage($chatId, "❌ Có lỗi xảy ra: " . $e->getMessage());
        }
    }

    /**
     * Xử lý lệnh cập nhật DNS: updatedns:domain:ns1:ns2
     */
    protected function processUpdateDNS(string $chatId, string $domain, string $ns1, string $ns2): void
    {
        try {
            $history = \App\Models\History::where('domain', $domain)->first();
            if (!$history) {
                $this->telegramService->sendMessage($chatId, "❌ Không tìm thấy domain: <code>{$domain}</code>", 'HTML');
                return;
            }

            $oldNs1 = $history->ns1;
            $oldNs2 = $history->ns2;

            $history->ns1 = $ns1;
            $history->ns2 = $ns2;
            $history->ahihi = 0; // Đánh dấu đã cập nhật
            $history->save();

            $text = "✅ <b>CẬP NHẬT DNS THÀNH CÔNG</b>\n\n";
            $text .= "🌐 Domain: <code>{$domain}</code>\n";
            $text .= "📊 NS1 cũ: <code>{$oldNs1}</code>\n";
            $text .= "📊 NS1 mới: <code>{$ns1}</code>\n";
            $text .= "📊 NS2 cũ: <code>{$oldNs2}</code>\n";
            $text .= "📊 NS2 mới: <code>{$ns2}</code>\n\n";
            $text .= "⏰ DNS sẽ có hiệu lực sau 12-24h";

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🏠 Về menu chính', 'callback_data' => 'menu_back']]
                ]
            ];

            $this->telegramService->sendMessage($chatId, $text, 'HTML', $keyboard);
            
            Log::info('DNS updated via Telegram', [
                'domain' => $domain,
                'ns1' => $ns1,
                'ns2' => $ns2,
                'admin_chat_id' => $chatId
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing update DNS', ['error' => $e->getMessage()]);
            $this->telegramService->sendMessage($chatId, "❌ Có lỗi xảy ra: " . $e->getMessage());
        }
    }
}
