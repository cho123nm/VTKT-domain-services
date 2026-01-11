<?php
// Trả về mảng cấu hình các dịch vụ bên thứ ba
return [

    // Cấu hình Mailgun (email service)
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'), // Mailgun domain
        'secret' => env('MAILGUN_SECRET'), // Mailgun secret key
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'), // Mailgun API endpoint (mặc định: api.mailgun.net)
        'scheme' => 'https', // Scheme: https
    ],

    // Cấu hình Postmark (email service)
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'), // Postmark API token
    ],

    // Cấu hình Amazon SES (email service)
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'), // AWS Access Key ID
        'secret' => env('AWS_SECRET_ACCESS_KEY'), // AWS Secret Access Key
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'), // AWS region (mặc định: us-east-1)
    ],

    // Cấu hình CardVIP (cổng nạp thẻ)
    'cardvip' => [
        'api_key' => env('CARDVIP_API_KEY', ''), // CardVIP API key (mặc định: chuỗi rỗng)
        'callback' => env('CARDVIP_CALLBACK', env('APP_URL', 'http://localhost') . '/callback'), // Callback URL (mặc định: APP_URL/callback)
    ],

    // Cấu hình Telegram Bot
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN', ''), // Telegram bot token (mặc định: chuỗi rỗng)
        'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID', ''), // Telegram admin chat ID (mặc định: chuỗi rỗng)
    ],

];

