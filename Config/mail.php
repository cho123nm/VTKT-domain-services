<?php
// Trả về mảng cấu hình mail
return [

    /*
    |--------------------------------------------------------------------------
    | Mailer Mặc Định
    |--------------------------------------------------------------------------
    |
    | Tùy chọn này điều khiển mailer mặc định được dùng để gửi email
    | từ ứng dụng. Các mailer khác có thể được thiết lập và sử dụng khi cần;
    | tuy nhiên, mailer này sẽ được dùng mặc định.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'), // Mailer mặc định (từ .env hoặc 'smtp')

    /*
    |--------------------------------------------------------------------------
    | Cấu Hình Mailers
    |--------------------------------------------------------------------------
    |
    | Ở đây bạn có thể cấu hình tất cả các mailers được dùng bởi ứng dụng
    | cùng với các settings tương ứng. Một số ví dụ đã được cấu hình sẵn
    | và bạn có thể tự do thêm nhiều hơn. Driver có thể là một trong các
    | driver được Laravel hỗ trợ.
    |
    | Hỗ trợ: "smtp", "sendmail", "mailgun", "ses", "postmark",
    |         "log", "array", "failover"
    |
    */

    'mailers' => [
        // Cấu hình SMTP mailer
        'smtp' => [
            'transport' => 'smtp', // Transport: SMTP
            'host' => env('MAIL_HOST', 'smtp.mailtrap.io'), // SMTP host (mặc định: smtp.mailtrap.io)
            'port' => env('MAIL_PORT', 2525), // SMTP port (mặc định: 2525)
            'encryption' => env('MAIL_ENCRYPTION', 'tls'), // Mã hóa: tls hoặc ssl (mặc định: tls)
            'username' => env('MAIL_USERNAME'), // SMTP username
            'password' => env('MAIL_PASSWORD'), // SMTP password
            'timeout' => null, // Timeout (null = không giới hạn)
            'local_domain' => env('MAIL_EHLO_DOMAIN'), // Domain cho EHLO command
        ],

        // Cấu hình Amazon SES mailer
        'ses' => [
            'transport' => 'ses', // Transport: Amazon SES
        ],

        // Cấu hình Mailgun mailer
        'mailgun' => [
            'transport' => 'mailgun', // Transport: Mailgun
        ],

        // Cấu hình Postmark mailer
        'postmark' => [
            'transport' => 'postmark', // Transport: Postmark
        ],

        // Cấu hình Sendmail mailer
        'sendmail' => [
            'transport' => 'sendmail', // Transport: Sendmail
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'), // Đường dẫn sendmail command
        ],

        // Cấu hình Log mailer (ghi email vào log thay vì gửi thật)
        'log' => [
            'transport' => 'log', // Transport: Log
            'channel' => env('MAIL_LOG_CHANNEL'), // Log channel (nếu có)
        ],

        // Cấu hình Array mailer (lưu email vào mảng trong memory, dùng cho testing)
        'array' => [
            'transport' => 'array', // Transport: Array
        ],

        // Cấu hình Failover mailer (thử mailer đầu tiên, nếu fail thì dùng mailer tiếp theo)
        'failover' => [
            'transport' => 'failover', // Transport: Failover
            'mailers' => [
                'smtp', // Thử SMTP trước
                'log', // Nếu SMTP fail, dùng Log
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Địa Chỉ "From" Toàn Cục
    |--------------------------------------------------------------------------
    |
    | Bạn có thể muốn tất cả email được gửi từ cùng một địa chỉ.
    | Ở đây, bạn có thể chỉ định tên và địa chỉ được dùng toàn cục
    | cho tất cả email được gửi bởi ứng dụng.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'), // Địa chỉ email gửi đi (mặc định: hello@example.com)
        'name' => env('MAIL_FROM_NAME', 'Example'), // Tên người gửi (mặc định: Example)
    ],

    /*
    |--------------------------------------------------------------------------
    | Cài Đặt Markdown Mail
    |--------------------------------------------------------------------------
    |
    | Nếu bạn đang dùng Markdown để render email, bạn có thể cấu hình
    | markdown message renderer ở đây. Bạn có thể chỉ định theme và
    | Markdown renderer nào được dùng khi render email dựa trên Markdown.
    |
    */

    'markdown' => [
        'theme' => 'default', // Theme mặc định cho Markdown emails

        'paths' => [
            resource_path('views/vendor/mail'), // Đường dẫn đến các Markdown email templates
        ],
    ],

];

