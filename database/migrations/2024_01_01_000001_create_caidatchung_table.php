<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Database\Migrations\Migration; // Base class cho migration
use Illuminate\Database\Schema\Blueprint; // Class để định nghĩa cấu trúc bảng
use Illuminate\Support\Facades\Schema; // Facade để thao tác với database schema

/**
 * Migration tạo bảng caidatchung (cài đặt chung)
 * Bảng này lưu trữ các cài đặt chung của hệ thống (theme, logo, API keys, etc.)
 */
return new class extends Migration
{
    /**
     * Chạy migration - tạo bảng caidatchung
     * 
     * @return void
     */
    public function up(): void
    {
        // Tạo bảng caidatchung
        Schema::create('caidatchung', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng (primary key)
            $table->string('tieude', 255)->nullable(); // Tiêu đề website (có thể null)
            $table->string('theme', 255)->nullable(); // Theme của website (có thể null)
            $table->text('keywords')->nullable(); // Từ khóa SEO (có thể null)
            $table->text('mota')->nullable(); // Mô tả website (có thể null)
            $table->string('imagebanner', 255)->nullable(); // Ảnh banner (có thể null)
            $table->string('sodienthoai', 255)->nullable(); // Số điện thoại (có thể null)
            $table->string('banner', 2555)->nullable(); // Banner (có thể null)
            $table->string('logo', 2555)->nullable(); // Logo (có thể null)
            $table->string('webgach', 2565)->nullable(); // Web gạch (có thể null)
            $table->string('apikey', 2555)->nullable(); // API key cho cổng nạp thẻ (có thể null)
            $table->string('callback', 255)->nullable(); // Callback URL cho cổng nạp thẻ (có thể null)
            $table->string('facebook_link', 500)->nullable(); // Link Facebook (có thể null)
            $table->string('zalo_phone', 50)->nullable(); // Số điện thoại Zalo (có thể null)
            $table->string('telegram_bot_token', 255)->nullable()->comment('Telegram Bot Token'); // Telegram bot token (có thể null)
            $table->string('telegram_admin_chat_id', 255)->nullable()->comment('Telegram Admin Chat ID'); // Telegram admin chat ID (có thể null)
            $table->index('theme'); // Tạo index cho cột theme để tìm kiếm nhanh hơn
        });
    }

    /**
     * Rollback migration - xóa bảng caidatchung
     * 
     * @return void
     */
    public function down(): void
    {
        // Xóa bảng caidatchung nếu tồn tại
        Schema::dropIfExists('caidatchung');
    }
};

