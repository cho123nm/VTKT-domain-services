<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetTelegramWebhookInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:get-webhook-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get current Telegram webhook information';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $botToken = config('services.telegram.bot_token');

        if (empty($botToken)) {
            $this->error('Telegram bot token is not configured in .env file');
            $this->info('Please set TELEGRAM_BOT_TOKEN in your .env file');
            return 1;
        }

        try {
            $response = Http::timeout(30)
                ->get("https://api.telegram.org/bot{$botToken}/getWebhookInfo");

            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['ok'] ?? false) {
                    $info = $result['result'] ?? [];
                    
                    $this->info('📋 Telegram Webhook Information:');
                    $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                    $this->info('URL: ' . ($info['url'] ?? 'Not set'));
                    $this->info('Has custom certificate: ' . ($info['has_custom_certificate'] ?? false ? 'Yes' : 'No'));
                    $this->info('Pending update count: ' . ($info['pending_update_count'] ?? 0));
                    
                    if (!empty($info['last_error_date'])) {
                        $this->warn('Last error date: ' . date('Y-m-d H:i:s', $info['last_error_date']));
                        $this->warn('Last error message: ' . ($info['last_error_message'] ?? 'N/A'));
                    }
                    
                    if (!empty($info['last_synchronization_error_date'])) {
                        $this->warn('Last sync error date: ' . date('Y-m-d H:i:s', $info['last_synchronization_error_date']));
                    }
                    
                    $this->info('Max connections: ' . ($info['max_connections'] ?? 40));
                    $this->info('Allowed updates: ' . json_encode($info['allowed_updates'] ?? []));
                    $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                    
                    return 0;
                } else {
                    $this->error('❌ Failed to get webhook info');
                    $this->error('Error: ' . ($result['description'] ?? 'Unknown error'));
                    return 1;
                }
            } else {
                $this->error('❌ HTTP Error: ' . $response->status());
                $this->error('Response: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Exception: ' . $e->getMessage());
            Log::error('Telegram webhook info error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
