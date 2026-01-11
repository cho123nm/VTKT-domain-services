<?php
// Khai báo namespace cho Console Kernel này - thuộc App\Console
namespace App\Console;

// Import Schedule class và ConsoleKernel base class
use Illuminate\Console\Scheduling\Schedule; // Class để định nghĩa lịch chạy command
use Illuminate\Foundation\Console\Kernel as ConsoleKernel; // Base class cho Console Kernel

/**
 * Class Kernel
 * Console Kernel của ứng dụng
 * Quản lý Artisan commands và scheduled tasks
 */
class Kernel extends ConsoleKernel
{
    /**
     * Định nghĩa lịch chạy command của ứng dụng
     * Các command được định nghĩa ở đây sẽ tự động chạy theo lịch đã set
     * 
     * @param Schedule $schedule - Schedule instance để định nghĩa lịch
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ví dụ: chạy command 'inspire' mỗi giờ (đã comment)
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Đăng ký các commands cho ứng dụng
     * Load tất cả commands từ thư mục Commands và routes/console.php
     * 
     * @return void
     */
    protected function commands(): void
    {
        // Load tất cả commands từ thư mục app/Console/Commands
        $this->load(__DIR__.'/Commands');

        // Load routes từ routes/console.php (nếu có)
        require base_path('routes/console.php');
    }
}

