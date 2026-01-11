<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Database\Migrations\Migration; // Base class cho migration
use Illuminate\Database\Schema\Blueprint; // Class để định nghĩa cấu trúc bảng
use Illuminate\Support\Facades\Schema; // Facade để thao tác với database schema

/**
 * Migration tạo bảng users (người dùng)
 * Bảng này lưu trữ thông tin người dùng của hệ thống
 */
return new class extends Migration
{
    /**
     * Chạy migration - tạo bảng users
     * 
     * @return void
     */
    public function up(): void
    {
        // Tạo bảng users
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng (primary key)
            $table->string('taikhoan', 255)->nullable(); // Tên tài khoản (có thể null)
            $table->string('matkhau', 255)->nullable(); // Mật khẩu (đã hash) (có thể null)
            $table->string('email', 255)->nullable(); // Email (có thể null)
            $table->integer('tien')->default(0); // Số dư tài khoản (mặc định: 0)
            $table->integer('chucvu')->default(0); // Chức vụ: 0 = user, 1 = admin (mặc định: 0)
            $table->string('time', 255)->nullable(); // Thời gian đăng ký (có thể null)
            $table->index('taikhoan'); // Tạo index cho cột taikhoan để tìm kiếm nhanh hơn
            $table->index('email'); // Tạo index cho cột email để tìm kiếm nhanh hơn
            $table->index('chucvu'); // Tạo index cho cột chucvu để filter nhanh hơn
        });
    }

    /**
     * Rollback migration - xóa bảng users
     * 
     * @return void
     */
    public function down(): void
    {
        // Xóa bảng users nếu tồn tại
        Schema::dropIfExists('users');
    }
};

