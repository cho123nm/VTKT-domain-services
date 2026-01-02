<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Feedback;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Handle incoming Telegram webhook
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        try {
            // Log webhook data for debugging
            Log::info('Telegram webhook received', [
                'data' => $request->all()
            ]);

            $update = $request->all();

            if (empty($update)) {
                Log::warning('Telegram webhook: Empty update data');
                return response('Invalid request', 400);
            }

            $message = $update['message'] ?? null;
            $callbackQuery = $update['callback_query'] ?? null;

            // Process message
            if ($message) {
                $this->processMessage($message);
            }

            // Process callback query (if needed in future)
            if ($callbackQuery) {
                $this->processCallbackQuery($callbackQuery);
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Process incoming message from Telegram
     * 
     * @param array $message
     * @return void
     */
    protected function processMessage(array $message): void
    {
        $chatId = $message['chat']['id'] ?? null;
        $text = $message['text'] ?? '';
        $from = $message['from'] ?? [];
        $username = $from['username'] ?? $from['first_name'] ?? 'Unknown';

        if (!$chatId) {
            Log::warning('Telegram message: Missing chat ID');
            return;
        }

        Log::info('Telegram message received', [
            'chat_id' => $chatId,
            'username' => $username,
            'text' => $text
        ]);

        // Handle /start command
        if (strpos($text, '/start') === 0) {
            $this->handleStartCommand($chatId);
            return;
        }

        // Handle /help command
        if (strpos($text, '/help') === 0) {
            $this->handleHelpCommand($chatId);
            return;
        }

        // Process feedback message
        $this->processFeedbackMessage($chatId, $text, $from);
    }

    /**
     * Handle /start command
     * 
     * @param string $chatId
     * @return void
     */
    protected function handleStartCommand(string $chatId): void
    {
        $message = "👋 Chào mừng bạn đến với hệ thống hỗ trợ!\n\n" .
                   "Để gửi phản hồi, vui lòng nhập theo format:\n\n" .
                   "📧 Email của bạn\n\n" .
                   "❌ Mô tả lỗi/phản hồi\n\n" .
                   "Ví dụ:\n" .
                   "email@example.com\n\n" .
                   "Tôi gặp lỗi khi thanh toán";

        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Handle /help command
     * 
     * @param string $chatId
     * @return void
     */
    protected function handleHelpCommand(string $chatId): void
    {
        $message = "📋 HƯỚNG DẪN GỬI PHẢN HỒI\n\n" .
                   "1. Nhập email của bạn\n" .
                   "2. Nhấn Enter\n" .
                   "3. Nhập mô tả lỗi/phản hồi\n\n" .
                   "Hoặc gửi theo format:\n\n" .
                   "📧 Email\n\n" .
                   "❌ Mô tả lỗi";

        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Process feedback message from user
     * 
     * @param string $chatId
     * @param string $text
     * @param array $from
     * @return void
     */
    protected function processFeedbackMessage(string $chatId, string $text, array $from): void
    {
        // Parse email and feedback content from message
        $lines = explode("\n", $text);
        $email = '';
        $feedbackMessage = '';

        // Find email in message
        foreach ($lines as $line) {
            $line = trim($line);
            if (filter_var($line, FILTER_VALIDATE_EMAIL)) {
                $email = $line;
                break;
            }
        }

        // If no email found, use first line as email (may not be valid format)
        if (empty($email) && !empty($lines[0])) {
            $email = trim($lines[0]);
        }

        // Get feedback content (excluding email line)
        $feedbackLines = [];
        $foundEmail = false;
        foreach ($lines as $line) {
            $line = trim($line);
            if (filter_var($line, FILTER_VALIDATE_EMAIL)) {
                $foundEmail = true;
                continue;
            }
            if ($foundEmail || !empty($line)) {
                $feedbackLines[] = $line;
            }
        }

        $feedbackMessage = implode("\n", $feedbackLines);

        // If no content, use entire message
        if (empty($feedbackMessage)) {
            $feedbackMessage = $text;
        }

        // Find user in database by email
        $user = null;
        $userId = 0;
        $username = $from['first_name'] ?? $from['username'] ?? 'Unknown';

        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $userId = $user->id;
                $username = $user->taikhoan;
            }
        }

        // If user not found, use Telegram username
        if (!$user) {
            $email = $email ?: ($from['username'] ?? '') . '@telegram';
        }

        // Save feedback to database
        try {
            $time = date('d/m/Y - H:i:s');

            $feedback = Feedback::create([
                'uid' => $userId,
                'username' => $username,
                'email' => $email,
                'message' => $feedbackMessage,
                'telegram_chat_id' => (string)$chatId,
                'time' => $time,
                'status' => 0 // Unread
            ]);

            // Send confirmation to user
            $confirmMessage = "✅ Phản hồi của bạn đã được gửi thành công!\n\n" .
                            "Chúng tôi sẽ xem xét và phản hồi sớm nhất có thể.\n\n" .
                            "📧 Email: " . $email;

            $this->telegramService->sendMessage($chatId, $confirmMessage);

            // Send notification to admin
            $this->telegramService->notifyNewFeedback([
                'username' => $username,
                'title' => 'Phản hồi từ Telegram',
                'content' => $feedbackMessage,
                'time' => $time
            ]);

            Log::info('Telegram feedback saved', [
                'feedback_id' => $feedback->id,
                'user_id' => $userId,
                'email' => $email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save Telegram feedback', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId
            ]);

            $errorMessage = "❌ Có lỗi xảy ra khi gửi phản hồi. Vui lòng thử lại sau.";
            $this->telegramService->sendMessage($chatId, $errorMessage);
        }
    }

    /**
     * Process callback query (for future use)
     * 
     * @param array $callbackQuery
     * @return void
     */
    protected function processCallbackQuery(array $callbackQuery): void
    {
        // Handle callback queries if needed in future
        Log::info('Telegram callback query received', [
            'data' => $callbackQuery
        ]);
    }
}
