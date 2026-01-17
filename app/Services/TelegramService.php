<?php
// Khai báo namespace cho Service này - thuộc App\Services
namespace App\Services;

// Import các Facade cần thiết
use Illuminate\Support\Facades\Http; // Facade để gửi HTTP request
use Illuminate\Support\Facades\Log; // Facade để ghi log
use App\Models\Settings; // Model quản lý cài đặt hệ thống

/**
 * Class TelegramService
 * Service xử lý gửi thông báo qua Telegram Bot API
 * Dùng để thông báo cho admin về đơn hàng mới, feedback mới, etc.
 */
class TelegramService
{
    // Thuộc tính lưu trữ bot token từ config
    protected $botToken;
    // Thuộc tính lưu trữ chat ID của admin
    protected $adminChatId;
    // Thuộc tính lưu trữ URL API của Telegram
    protected $apiUrl;

    /**
     * Hàm khởi tạo (Constructor)
     * Lấy cấu hình từ database (Settings) hoặc config và tạo API URL
     */
    public function __construct()
    {
        // Ưu tiên lấy từ database (Settings), nếu không có thì lấy từ config
        $settings = Settings::getOne();
        if ($settings) {
            // Lấy bot token từ database
            $this->botToken = $settings->telegram_bot_token ?? '';
            // Lấy admin chat ID từ database
            $this->adminChatId = $settings->telegram_admin_chat_id ?? '';
        } else {
            // Nếu không có settings trong database, lấy từ config
            $this->botToken = config('services.telegram.bot_token', '');
            $this->adminChatId = config('services.telegram.admin_chat_id', '');
        }
        
        // Tạo URL API Telegram
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Gửi tin nhắn đến Telegram chat
     * 
     * @param string $chatId - ID của chat/người nhận (ví dụ: 123456789)
     * @param string $message - Nội dung tin nhắn
     * @param string $parseMode - Chế độ parse: 'HTML' hoặc 'Markdown' (mặc định: 'HTML')
     * @return array ['success' => bool, 'message' => string] - Kết quả gửi tin nhắn
     */
    public function sendMessage(string $chatId, string $message, string $parseMode = 'HTML'): array
    {
        // Kiểm tra bot token đã được cấu hình chưa
        if (empty($this->botToken)) {
            // Ghi log cảnh báo nếu chưa cấu hình
            Log::warning('Telegram bot token not configured');
            return [
                'success' => false,
                'message' => 'Telegram bot token not configured'
            ];
        }

        // Thử gửi tin nhắn qua Telegram API
        try {
            // Gửi POST request đến Telegram API với timeout 10 giây
            $response = Http::timeout(10)
                ->post("{$this->apiUrl}/sendMessage", [
                    'chat_id' => $chatId, // ID chat/người nhận
                    'text' => $message, // Nội dung tin nhắn
                    'parse_mode' => $parseMode // Chế độ parse: HTML hoặc Markdown
                ]);

            // Kiểm tra response thành công (status code 200-299)
            if ($response->successful()) {
                // Parse JSON response từ Telegram API
                $result = $response->json();
                
                // Kiểm tra Telegram API trả về ok = true
                if ($result['ok'] ?? false) {
                    // Trả về thành công
                    return [
                        'success' => true,
                        'message' => 'Message sent successfully'
                    ];
                }
                
                // Nếu Telegram API trả về lỗi, ghi log và trả về lỗi
                Log::error('Telegram API returned error', ['result' => $result]);
                return [
                    'success' => false,
                    'message' => $result['description'] ?? 'Unknown error' // Thông báo lỗi từ Telegram
                ];
            }

            // Nếu HTTP status code không thành công, ghi log lỗi
            Log::error('Telegram API HTTP error', [
                'status' => $response->status(), // HTTP status code
                'body' => $response->body() // Response body
            ]);

            // Trả về lỗi HTTP
            return [
                'success' => false,
                'message' => 'HTTP error: ' . $response->status()
            ];
        } catch (\Exception $e) {
            // Nếu có exception (network error, timeout, etc.), ghi log chi tiết
            Log::error('Telegram API Exception', [
                'message' => $e->getMessage(), // Thông báo lỗi
                'trace' => $e->getTraceAsString() // Stack trace để debug
            ]);

            // Trả về lỗi exception
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Thông báo cho admin về đơn hàng mới
     * 
     * @param string $orderType - Loại đơn hàng: 'domain', 'hosting', 'vps', 'sourcecode'
     * @param array $orderDetails - Chi tiết đơn hàng (username, mgd, domain, etc.)
     * @return array ['success' => bool, 'message' => string] - Kết quả gửi thông báo
     */
    public function notifyNewOrder(string $orderType, array $orderDetails): array
    {
        // Kiểm tra admin chat ID đã được cấu hình chưa
        if (empty($this->adminChatId)) {
            // Ghi log cảnh báo nếu chưa cấu hình
            Log::warning('Telegram admin chat ID not configured');
            return [
                'success' => false,
                'message' => 'Admin chat ID not configured'
            ];
        }

        // Xây dựng nội dung tin nhắn dựa trên loại đơn hàng
        $message = $this->buildOrderMessage($orderType, $orderDetails);

        // Gửi tin nhắn đến admin chat ID
        return $this->sendMessage($this->adminChatId, $message);
    }

    /**
     * Thông báo cho admin về feedback mới
     * 
     * @param array $feedbackDetails - Chi tiết feedback (username, title, content, time)
     * @return array ['success' => bool, 'message' => string] - Kết quả gửi thông báo
     */
    public function notifyNewFeedback(array $feedbackDetails): array
    {
        // Kiểm tra admin chat ID đã được cấu hình chưa
        if (empty($this->adminChatId)) {
            // Ghi log cảnh báo nếu chưa cấu hình
            Log::warning('Telegram admin chat ID not configured');
            return [
                'success' => false,
                'message' => 'Admin chat ID not configured'
            ];
        }

        // Xây dựng nội dung tin nhắn feedback
        $message = $this->buildFeedbackMessage($feedbackDetails);

        // Gửi tin nhắn đến admin chat ID
        return $this->sendMessage($this->adminChatId, $message);
    }

    /**
     * Xây dựng nội dung tin nhắn thông báo đơn hàng
     * Protected method - chỉ được gọi từ trong class này
     * 
     * @param string $orderType - Loại đơn hàng: 'domain', 'hosting', 'vps', 'sourcecode'
     * @param array $orderDetails - Chi tiết đơn hàng
     * @return string - Nội dung tin nhắn đã được format
     */
    protected function buildOrderMessage(string $orderType, array $orderDetails): string
    {
        // Lấy thông tin cơ bản từ orderDetails, mặc định 'N/A' nếu không có
        $username = $orderDetails['username'] ?? 'N/A'; // Username người dùng
        $mgd = $orderDetails['mgd'] ?? 'N/A'; // Mã giao dịch
        $time = $orderDetails['time'] ?? date('d/m/Y - H:i:s'); // Thời gian (mặc định thời gian hiện tại)

        // Xây dựng nội dung tin nhắn dựa trên loại đơn hàng
        switch ($orderType) {
            case 'domain':
                // Lấy thông tin domain từ orderDetails
                $domain = $orderDetails['domain'] ?? 'N/A'; // Tên domain
                $ns1 = $orderDetails['ns1'] ?? 'N/A'; // Nameserver 1
                $ns2 = $orderDetails['ns2'] ?? 'N/A'; // Nameserver 2
                
                // Trả về tin nhắn format HTML cho Telegram
                return "🌐 <b>ĐƠN HÀNG MỚI - DOMAIN</b>\n\n" .
                       "👤 Khách hàng: <code>{$username}</code>\n" .
                       "🔖 Mã giao dịch: <code>{$mgd}</code>\n" .
                       "🌍 Tên miền: <code>{$domain}</code>\n" .
                       "🔧 NS1: <code>{$ns1}</code>\n" .
                       "🔧 NS2: <code>{$ns2}</code>\n" .
                       "⏰ Thời gian: {$time}";

            case 'hosting':
                // Lấy thông tin hosting từ orderDetails
                $packageName = $orderDetails['package_name'] ?? 'N/A'; // Tên gói hosting
                $period = $orderDetails['period'] ?? 'N/A'; // Thời hạn (tháng)
                $domain = $orderDetails['domain'] ?? 'N/A'; // Domain (nếu có)
                
                // Trả về tin nhắn format HTML cho Telegram
                return "🖥️ <b>ĐƠN HÀNG MỚI - HOSTING</b>\n\n" .
                       "👤 Khách hàng: <code>{$username}</code>\n" .
                       "🔖 Mã giao dịch: <code>{$mgd}</code>\n" .
                       "📦 Gói: <b>{$packageName}</b>\n" .
                       "⏳ Thời hạn: {$period} tháng\n" .
                       "🌍 Domain: <code>{$domain}</code>\n" .
                       "⏰ Thời gian: {$time}";

            case 'vps':
                // Lấy thông tin VPS từ orderDetails
                $packageName = $orderDetails['package_name'] ?? 'N/A'; // Tên gói VPS
                $period = $orderDetails['period'] ?? 'N/A'; // Thời hạn (tháng)
                
                // Trả về tin nhắn format HTML cho Telegram
                return "💻 <b>ĐƠN HÀNG MỚI - VPS</b>\n\n" .
                       "👤 Khách hàng: <code>{$username}</code>\n" .
                       "🔖 Mã giao dịch: <code>{$mgd}</code>\n" .
                       "📦 Gói: <b>{$packageName}</b>\n" .
                       "⏳ Thời hạn: {$period} tháng\n" .
                       "⏰ Thời gian: {$time}";

            case 'sourcecode':
                // Lấy thông tin source code từ orderDetails
                $productName = $orderDetails['product_name'] ?? 'N/A'; // Tên sản phẩm
                
                // Trả về tin nhắn format HTML cho Telegram
                return "📦 <b>ĐƠN HÀNG MỚI - SOURCE CODE</b>\n\n" .
                       "👤 Khách hàng: <code>{$username}</code>\n" .
                       "🔖 Mã giao dịch: <code>{$mgd}</code>\n" .
                       "📝 Sản phẩm: <b>{$productName}</b>\n" .
                       "⏰ Thời gian: {$time}";

            default:
                // Trường hợp không xác định được loại đơn hàng
                return "📋 <b>ĐƠN HÀNG MỚI</b>\n\n" .
                       "👤 Khách hàng: <code>{$username}</code>\n" .
                       "🔖 Mã giao dịch: <code>{$mgd}</code>\n" .
                       "⏰ Thời gian: {$time}";
        }
    }

    /**
     * Xây dựng nội dung tin nhắn thông báo feedback
     * Protected method - chỉ được gọi từ trong class này
     * 
     * @param array $feedbackDetails - Chi tiết feedback (username, title, content, time)
     * @return string - Nội dung tin nhắn đã được format
     */
    protected function buildFeedbackMessage(array $feedbackDetails): string
    {
        // Lấy thông tin feedback từ mảng, mặc định 'N/A' nếu không có
        $username = $feedbackDetails['username'] ?? 'N/A'; // Username người gửi feedback
        $title = $feedbackDetails['title'] ?? 'N/A'; // Tiêu đề feedback
        $content = $feedbackDetails['content'] ?? 'N/A'; // Nội dung feedback
        $time = $feedbackDetails['time'] ?? date('d/m/Y - H:i:s'); // Thời gian (mặc định thời gian hiện tại)

        // Cắt ngắn nội dung nếu quá dài (Telegram có giới hạn độ dài tin nhắn)
        if (strlen($content) > 200) {
            // Chỉ lấy 200 ký tự đầu và thêm '...'
            $content = substr($content, 0, 200) . '...';
        }

        // Trả về tin nhắn format HTML cho Telegram
        return "💬 <b>PHẢN HỒI MỚI</b>\n\n" .
               "👤 Từ: <code>{$username}</code>\n" .
               "📌 Tiêu đề: <b>{$title}</b>\n" .
               "📝 Nội dung:\n{$content}\n\n" .
               "⏰ Thời gian: {$time}";
    }
}
