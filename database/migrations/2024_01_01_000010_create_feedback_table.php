<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Database\Migrations\Migration; // Base class cho migration
use Illuminate\Database\Schema\Blueprint; // Class để định nghĩa cấu trúc bảng
use Illuminate\Support\Facades\Schema; // Facade để thao tác với database schema

/**
 * Migration tạo bảng feedback (phản hồi)
 * Bảng này lưu trữ các phản hồi/lỗi từ users và phản hồi từ admin
 */
return new class extends Migration
{
    /**
     * Chạy migration - tạo bảng feedback
     * 
     * @return void
     */
    public function up(): void
    {
        // Tạo bảng feedback
        Schema::create('feedback', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng (primary key)
            $table->unsignedBigInteger('uid')->nullable()->comment('ID người dùng gửi phản hồi'); // ID user (foreign key đến users.id) (có thể null)
            $table->string('username', 255)->nullable()->comment('Tên người dùng'); // Tên người dùng (có thể null)
            $table->string('email', 255)->nullable()->comment('Email người dùng'); // Email người dùng (có thể null)
            $table->text('message')->nullable()->comment('Nội dung phản hồi/lỗi'); // Nội dung phản hồi/lỗi (có thể null)
            $table->text('admin_reply')->nullable()->comment('Phản hồi từ admin'); // Phản hồi từ admin (có thể null)
            $table->integer('status')->default(0)->comment('0: Chờ xử lý, 1: Đã trả lời, 2: Đã đọc'); // Trạng thái: 0 = Chờ xử lý, 1 = Đã trả lời, 2 = Đã đọc (mặc định: 0)
            $table->string('telegram_chat_id', 255)->nullable()->comment('Chat ID Telegram của user'); // Chat ID Telegram để gửi phản hồi (có thể null)
            $table->string('time', 255)->nullable()->comment('Thời gian gửi'); // Thời gian gửi feedback (có thể null)
            $table->string('reply_time', 255)->nullable()->comment('Thời gian admin trả lời'); // Thời gian admin trả lời (có thể null)
            $table->index('uid'); // Tạo index cho cột uid để tìm kiếm nhanh hơn
            $table->index('status'); // Tạo index cho cột status để filter nhanh hơn
        });
    }

    /**
     * Rollback migration - xóa bảng feedback
     * 
     * @return void
     */
    public function down(): void
    {
        // Xóa bảng feedback nếu tồn tại
        Schema::dropIfExists('feedback');
    }
};

