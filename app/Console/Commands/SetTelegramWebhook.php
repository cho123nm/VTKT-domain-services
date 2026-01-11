<?php
// Khai báo namespace cho Command này - thuộc App\Console\Commands
namespace App\Console\Commands;

// Import Command base class và các Facade cần thiết
use Illuminate\Console\Command; // Base class cho Artisan command
use Illuminate\Support\Facades\Http; // Facade để gửi HTTP request
use Illuminate\Support\Facades\Log; // Facade để ghi log

/**
 * Class SetTelegramWebhook
 * Artisan command để thiết lập Telegram webhook URL cho bot
 * Webhook cho phép Telegram gửi updates đến server của bạn
 */
class SetTelegramWebhook extends Command
{
    /**
     * Tên và signature của command
     * Signature định nghĩa cách gọi command: php artisan telegram:set-webhook [url]
     * {url?} nghĩa là URL là tham số tùy chọn
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook {url?}';

    /**
     * Mô tả command (hiển thị khi chạy php artisan list)
     *
     * @var string
     */
    protected $description = 'Thiết lập Telegram webhook URL cho bot';

    /**
     * Thực thi command
     * Thiết lập webhook URL trên Telegram Bot API
     *
     * @return int - Exit code (0 = thành công, 1 = lỗi)
     */
    public function handle()
    {
        // Lấy bot token từ config
        $botToken = config('services.telegram.bot_token');

        // Kiểm tra bot token có được cấu hình chưa
        if (empty($botToken)) {
            $this->error('Telegram bot token chưa được cấu hình trong file .env');
            $this->info('Vui lòng set TELEGRAM_BOT_TOKEN trong file .env của bạn');
            return 1; // Exit code 1 = lỗi
        }

        // Lấy webhook URL từ argument hoặc tự động tạo từ APP_URL
        $webhookUrl = $this->argument('url'); // Lấy URL từ tham số command
        
        // Nếu không có URL trong argument, tự động tạo từ APP_URL
        if (empty($webhookUrl)) {
            $appUrl = config('app.url'); // Lấy APP_URL từ config
            $webhookUrl = rtrim($appUrl, '/') . '/telegram/webhook'; // Tạo URL webhook
        }

        // Hiển thị thông báo đang thiết lập webhook
        $this->info("Đang thiết lập Telegram webhook đến: {$webhookUrl}");

        try {
            // Thiết lập webhook trên Telegram Bot API
            $response = Http::timeout(30) // Timeout 30 giây
                ->post("https://api.telegram.org/bot{$botToken}/setWebhook", [
                    'url' => $webhookUrl, // URL webhook
                    'allowed_updates' => ['message', 'callback_query'] // Chỉ nhận message và callback_query
                ]);

            // Kiểm tra response thành công (HTTP 200)
            if ($response->successful()) {
                // Decode JSON response thành mảng PHP
                $result = $response->json();
                
                // Kiểm tra kết quả từ Telegram API
                if ($result['ok'] ?? false) {
                    // Nếu thành công, hiển thị thông báo
                    $this->info('✅ Webhook đã được thiết lập thành công!');
                    $this->info('Mô tả: ' . ($result['description'] ?? 'N/A'));
                    
                    // Lấy thông tin webhook để xác minh
                    $this->info("\nĐang xác minh webhook...");
                    $this->call('telegram:get-webhook-info'); // Gọi command khác để lấy thông tin webhook
                    
                    return 0; // Exit code 0 = thành công
                } else {
                    // Nếu thất bại, hiển thị lỗi
                    $this->error('❌ Không thể thiết lập webhook');
                    $this->error('Lỗi: ' . ($result['description'] ?? 'Lỗi không xác định'));
                    return 1; // Exit code 1 = lỗi
                }
            } else {
                // Nếu HTTP status code không phải 200, hiển thị lỗi HTTP
                $this->error('❌ Lỗi HTTP: ' . $response->status());
                $this->error('Response: ' . $response->body());
                return 1; // Exit code 1 = lỗi
            }
        } catch (\Exception $e) {
            // Nếu có exception, hiển thị lỗi và ghi log
            $this->error('❌ Exception: ' . $e->getMessage());
            Log::error('Telegram webhook setup error', [
                'message' => $e->getMessage(), // Thông báo lỗi
                'trace' => $e->getTraceAsString() // Stack trace
            ]);
            return 1; // Exit code 1 = lỗi
        }
    }
}
