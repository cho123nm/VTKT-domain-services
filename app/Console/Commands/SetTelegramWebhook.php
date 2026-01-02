<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SetTelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook {url?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Telegram webhook URL for the bot';

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

        // Get webhook URL from argument or generate from APP_URL
        $webhookUrl = $this->argument('url');
        
        if (empty($webhookUrl)) {
            $appUrl = config('app.url');
            $webhookUrl = rtrim($appUrl, '/') . '/telegram/webhook';
        }

        $this->info("Setting Telegram webhook to: {$webhookUrl}");

        try {
            // Set webhook
            $response = Http::timeout(30)
                ->post("https://api.telegram.org/bot{$botToken}/setWebhook", [
                    'url' => $webhookUrl,
                    'allowed_updates' => ['message', 'callback_query']
                ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['ok'] ?? false) {
                    $this->info('✅ Webhook set successfully!');
                    $this->info('Description: ' . ($result['description'] ?? 'N/A'));
                    
                    // Get webhook info to verify
                    $this->info("\nVerifying webhook...");
                    $this->call('telegram:get-webhook-info');
                    
                    return 0;
                } else {
                    $this->error('❌ Failed to set webhook');
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
            Log::error('Telegram webhook setup error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
