<?php
// Khai báo namespace cho Command này - thuộc App\Console\Commands
namespace App\Console\Commands;

// Import Command base class và các Facade cần thiết
use Illuminate\Console\Command; // Base class cho Artisan command
use Illuminate\Support\Facades\Http; // Facade để gửi HTTP request
use Illuminate\Support\Facades\Log; // Facade để ghi log
use App\Models\Settings; // Model quản lý cài đặt hệ thống

/**
 * Class GetTelegramWebhookInfo
 * Artisan command để lấy thông tin Telegram webhook hiện tại
 * Hiển thị URL, trạng thái, lỗi (nếu có) của webhook
 */
class GetTelegramWebhookInfo extends Command
{
    /**
     * Tên và signature của command
     * Signature định nghĩa cách gọi command: php artisan telegram:get-webhook-info
     *
     * @var string
     */
    protected $signature = 'telegram:get-webhook-info';

    /**
     * Mô tả command (hiển thị khi chạy php artisan list)
     *
     * @var string
     */
    protected $description = 'Lấy thông tin Telegram webhook hiện tại';

    /**
     * Thực thi command
     * Lấy thông tin webhook từ Telegram Bot API và hiển thị
     *
     * @return int - Exit code (0 = thành công, 1 = lỗi)
     */
    public function handle()
    {
        // Ưu tiên lấy bot token từ database (Settings), nếu không có thì lấy từ config
        $settings = Settings::getOne();
        if ($settings && !empty($settings->telegram_bot_token)) {
            $botToken = $settings->telegram_bot_token;
        } else {
            $botToken = config('services.telegram.bot_token');
        }

        // Kiểm tra bot token có được cấu hình chưa
        if (empty($botToken)) {
            $this->error('Telegram bot token chưa được cấu hình');
            $this->info('Vui lòng cấu hình Bot Token trong trang Cài Đặt Telegram');
            return 1;
        }

        try {
            // Gửi request đến Telegram API để lấy thông tin webhook
            $response = Http::timeout(30) // Timeout 30 giây
                ->get("https://api.telegram.org/bot{$botToken}/getWebhookInfo");

            // Kiểm tra response thành công (HTTP 200)
            if ($response->successful()) {
                // Decode JSON response thành mảng PHP
                $result = $response->json();
                
                // Kiểm tra kết quả từ Telegram API
                if ($result['ok'] ?? false) {
                    // Lấy thông tin webhook từ result
                    $info = $result['result'] ?? [];
                    
                    // Hiển thị thông tin webhook
                    $this->info('📋 Thông Tin Telegram Webhook:');
                    $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                    $this->info('URL: ' . ($info['url'] ?? 'Chưa thiết lập')); // URL webhook
                    $this->info('Có chứng chỉ tùy chỉnh: ' . ($info['has_custom_certificate'] ?? false ? 'Có' : 'Không')); // Có custom certificate không
                    $this->info('Số lượng update đang chờ: ' . ($info['pending_update_count'] ?? 0)); // Số update chưa xử lý
                    
                    // Hiển thị thông tin lỗi nếu có
                    if (!empty($info['last_error_date'])) {
                        $this->warn('Ngày lỗi cuối cùng: ' . date('Y-m-d H:i:s', $info['last_error_date'])); // Ngày lỗi cuối cùng
                        $this->warn('Thông báo lỗi cuối cùng: ' . ($info['last_error_message'] ?? 'N/A')); // Thông báo lỗi
                    }
                    
                    // Hiển thị thông tin lỗi đồng bộ nếu có
                    if (!empty($info['last_synchronization_error_date'])) {
                        $this->warn('Ngày lỗi đồng bộ cuối cùng: ' . date('Y-m-d H:i:s', $info['last_synchronization_error_date'])); // Ngày lỗi đồng bộ
                    }
                    
                    $this->info('Số kết nối tối đa: ' . ($info['max_connections'] ?? 40)); // Số kết nối tối đa
                    $this->info('Các update được phép: ' . json_encode($info['allowed_updates'] ?? [])); // Loại update được phép
                    $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                    
                    return 0; // Exit code 0 = thành công
                } else {
                    // Nếu thất bại, hiển thị lỗi
                    $this->error('❌ Không thể lấy thông tin webhook');
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
            Log::error('Telegram webhook info error', [
                'message' => $e->getMessage(), // Thông báo lỗi
                'trace' => $e->getTraceAsString() // Stack trace
            ]);
            return 1; // Exit code 1 = lỗi
        }
    }
}
