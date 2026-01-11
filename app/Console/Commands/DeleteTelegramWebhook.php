<?php
// Khai báo namespace cho Command này - thuộc App\Console\Commands
namespace App\Console\Commands;

// Import Command base class và các Facade cần thiết
use Illuminate\Console\Command; // Base class cho Artisan command
use Illuminate\Support\Facades\Http; // Facade để gửi HTTP request
use Illuminate\Support\Facades\Log; // Facade để ghi log

/**
 * Class DeleteTelegramWebhook
 * Artisan command để xóa Telegram webhook
 * Hữu ích khi muốn test bot bằng getUpdates thay vì webhook
 */
class DeleteTelegramWebhook extends Command
{
    /**
     * Tên và signature của command
     * Signature định nghĩa cách gọi command: php artisan telegram:delete-webhook
     *
     * @var string
     */
    protected $signature = 'telegram:delete-webhook';

    /**
     * Mô tả command (hiển thị khi chạy php artisan list)
     *
     * @var string
     */
    protected $description = 'Xóa Telegram webhook (hữu ích khi test với getUpdates)';

    /**
     * Thực thi command
     * Xóa webhook từ Telegram Bot API
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

        // Xác nhận từ user trước khi xóa webhook
        if (!$this->confirm('Bạn có chắc chắn muốn xóa webhook không?')) {
            $this->info('Thao tác đã bị hủy');
            return 0; // Exit code 0 = thành công (nhưng không làm gì)
        }

        try {
            // Gửi request đến Telegram API để xóa webhook
            $response = Http::timeout(30) // Timeout 30 giây
                ->post("https://api.telegram.org/bot{$botToken}/deleteWebhook");

            // Kiểm tra response thành công (HTTP 200)
            if ($response->successful()) {
                // Decode JSON response thành mảng PHP
                $result = $response->json();
                
                // Kiểm tra kết quả từ Telegram API
                if ($result['ok'] ?? false) {
                    // Nếu thành công, hiển thị thông báo
                    $this->info('✅ Webhook đã được xóa thành công!');
                    $this->info('Mô tả: ' . ($result['description'] ?? 'N/A'));
                    return 0; // Exit code 0 = thành công
                } else {
                    // Nếu thất bại, hiển thị lỗi
                    $this->error('❌ Không thể xóa webhook');
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
            Log::error('Telegram webhook deletion error', [
                'message' => $e->getMessage(), // Thông báo lỗi
                'trace' => $e->getTraceAsString() // Stack trace
            ]);
            return 1; // Exit code 1 = lỗi
        }
    }
}
