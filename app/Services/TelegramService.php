<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $adminChatId;
    protected $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->adminChatId = config('services.telegram.admin_chat_id');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Send message to Telegram chat
     * 
     * @param string $chatId
     * @param string $message
     * @param string $parseMode
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendMessage(string $chatId, string $message, string $parseMode = 'HTML'): array
    {
        if (empty($this->botToken)) {
            Log::warning('Telegram bot token not configured');
            return [
                'success' => false,
                'message' => 'Telegram bot token not configured'
            ];
        }

        try {
            $response = Http::timeout(10)
                ->post("{$this->apiUrl}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => $parseMode
                ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['ok'] ?? false) {
                    return [
                        'success' => true,
                        'message' => 'Message sent successfully'
                    ];
                }
                
                Log::error('Telegram API returned error', ['result' => $result]);
                return [
                    'success' => false,
                    'message' => $result['description'] ?? 'Unknown error'
                ];
            }

            Log::error('Telegram API HTTP error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'HTTP error: ' . $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Telegram API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Notify admin about new order
     * 
     * @param string $orderType (domain, hosting, vps, sourcecode)
     * @param array $orderDetails
     * @return array ['success' => bool, 'message' => string]
     */
    public function notifyNewOrder(string $orderType, array $orderDetails): array
    {
        if (empty($this->adminChatId)) {
            Log::warning('Telegram admin chat ID not configured');
            return [
                'success' => false,
                'message' => 'Admin chat ID not configured'
            ];
        }

        // Build message based on order type
        $message = $this->buildOrderMessage($orderType, $orderDetails);

        return $this->sendMessage($this->adminChatId, $message);
    }

    /**
     * Notify admin about new feedback
     * 
     * @param array $feedbackDetails
     * @return array ['success' => bool, 'message' => string]
     */
    public function notifyNewFeedback(array $feedbackDetails): array
    {
        if (empty($this->adminChatId)) {
            Log::warning('Telegram admin chat ID not configured');
            return [
                'success' => false,
                'message' => 'Admin chat ID not configured'
            ];
        }

        $message = $this->buildFeedbackMessage($feedbackDetails);

        return $this->sendMessage($this->adminChatId, $message);
    }

    /**
     * Build order notification message
     * 
     * @param string $orderType
     * @param array $orderDetails
     * @return string
     */
    protected function buildOrderMessage(string $orderType, array $orderDetails): string
    {
        $username = $orderDetails['username'] ?? 'N/A';
        $mgd = $orderDetails['mgd'] ?? 'N/A';
        $time = $orderDetails['time'] ?? date('d/m/Y - H:i:s');

        switch ($orderType) {
            case 'domain':
                $domain = $orderDetails['domain'] ?? 'N/A';
                $ns1 = $orderDetails['ns1'] ?? 'N/A';
                $ns2 = $orderDetails['ns2'] ?? 'N/A';
                
                return "🌐 <b>ĐƠN HÀNG MỚI - DOMAIN</b>\n\n" .
                       "👤 Khách hàng: <code>{$username}</code>\n" .
                       "🔖 Mã giao dịch: <code>{$mgd}</code>\n" .
                       "🌍 Tên miền: <code>{$domain}</code>\n" .
                       "🔧 NS1: <code>{$ns1}</code>\n" .
                       "🔧 NS2: <code>{$ns2}</code>\n" .
                       "⏰ Thời gian: {$time}";

            case 'hosting':
                $packageName = $orderDetails['package_name'] ?? 'N/A';
                $period = $orderDetails['period'] ?? 'N/A';
                $domain = $orderDetails['domain'] ?? 'N/A';
                
                return "🖥️ <b>ĐƠN HÀNG MỚI - HOSTING</b>\n\n" .
                       "👤 Khách hàng: <code>{$username}</code>\n" .
                       "🔖 Mã giao dịch: <code>{$mgd}</code>\n" .
                       "📦 Gói: <b>{$packageName}</b>\n" .
                       "⏳ Thời hạn: {$period} tháng\n" .
                       "🌍 Domain: <code>{$domain}</code>\n" .
                       "⏰ Thời gian: {$time}";

            case 'vps':
                $packageName = $orderDetails['package_name'] ?? 'N/A';
                $period = $orderDetails['period'] ?? 'N/A';
                
                return "💻 <b>ĐƠN HÀNG MỚI - VPS</b>\n\n" .
                       "👤 Khách hàng: <code>{$username}</code>\n" .
                       "🔖 Mã giao dịch: <code>{$mgd}</code>\n" .
                       "📦 Gói: <b>{$packageName}</b>\n" .
                       "⏳ Thời hạn: {$period} tháng\n" .
                       "⏰ Thời gian: {$time}";

            case 'sourcecode':
                $productName = $orderDetails['product_name'] ?? 'N/A';
                
                return "📦 <b>ĐƠN HÀNG MỚI - SOURCE CODE</b>\n\n" .
                       "👤 Khách hàng: <code>{$username}</code>\n" .
                       "🔖 Mã giao dịch: <code>{$mgd}</code>\n" .
                       "📝 Sản phẩm: <b>{$productName}</b>\n" .
                       "⏰ Thời gian: {$time}";

            default:
                return "📋 <b>ĐƠN HÀNG MỚI</b>\n\n" .
                       "👤 Khách hàng: <code>{$username}</code>\n" .
                       "🔖 Mã giao dịch: <code>{$mgd}</code>\n" .
                       "⏰ Thời gian: {$time}";
        }
    }

    /**
     * Build feedback notification message
     * 
     * @param array $feedbackDetails
     * @return string
     */
    protected function buildFeedbackMessage(array $feedbackDetails): string
    {
        $username = $feedbackDetails['username'] ?? 'N/A';
        $title = $feedbackDetails['title'] ?? 'N/A';
        $content = $feedbackDetails['content'] ?? 'N/A';
        $time = $feedbackDetails['time'] ?? date('d/m/Y - H:i:s');

        // Truncate content if too long
        if (strlen($content) > 200) {
            $content = substr($content, 0, 200) . '...';
        }

        return "💬 <b>PHẢN HỒI MỚI</b>\n\n" .
               "👤 Từ: <code>{$username}</code>\n" .
               "📌 Tiêu đề: <b>{$title}</b>\n" .
               "📝 Nội dung:\n{$content}\n\n" .
               "⏰ Thời gian: {$time}";
    }
}
