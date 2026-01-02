<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeleteTelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:delete-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Telegram webhook (useful for testing with getUpdates)';

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

        if (!$this->confirm('Are you sure you want to delete the webhook?')) {
            $this->info('Operation cancelled');
            return 0;
        }

        try {
            $response = Http::timeout(30)
                ->post("https://api.telegram.org/bot{$botToken}/deleteWebhook");

            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['ok'] ?? false) {
                    $this->info('✅ Webhook deleted successfully!');
                    $this->info('Description: ' . ($result['description'] ?? 'N/A'));
                    return 0;
                } else {
                    $this->error('❌ Failed to delete webhook');
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
            Log::error('Telegram webhook deletion error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
