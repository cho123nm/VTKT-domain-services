<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Database\Migrations\Migration; // Base class cho migration
use Illuminate\Database\Schema\Blueprint; // Class để định nghĩa cấu trúc bảng
use Illuminate\Support\Facades\Schema; // Facade để thao tác với database schema

/**
 * Migration tạo bảng password_resets (đặt lại mật khẩu)
 * Bảng này lưu trữ các token để đặt lại mật khẩu khi user quên mật khẩu
 */
return new class extends Migration
{
    /**
     * Chạy migration - tạo bảng password_resets
     * 
     * @return void
     */
    public function up(): void
    {
        // Tạo bảng password_resets
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index(); // Email của user cần đặt lại mật khẩu (có index để tìm kiếm nhanh)
            $table->string('token'); // Token để đặt lại mật khẩu (bắt buộc)
            $table->timestamp('created_at')->nullable(); // Thời gian tạo token (có thể null)
        });
    }

    /**
     * Rollback migration - xóa bảng password_resets
     * 
     * @return void
     */
    public function down(): void
    {
        // Xóa bảng password_resets nếu tồn tại
        Schema::dropIfExists('password_resets');
    }
};

