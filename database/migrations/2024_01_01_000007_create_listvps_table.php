<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Database\Migrations\Migration; // Base class cho migration
use Illuminate\Database\Schema\Blueprint; // Class để định nghĩa cấu trúc bảng
use Illuminate\Support\Facades\Schema; // Facade để thao tác với database schema

/**
 * Migration tạo bảng listvps (danh sách gói VPS)
 * Bảng này lưu trữ thông tin các gói VPS được bán trên hệ thống
 */
return new class extends Migration
{
    /**
     * Chạy migration - tạo bảng listvps
     * 
     * @return void
     */
    public function up(): void
    {
        // Tạo bảng listvps
        Schema::create('listvps', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng (primary key)
            $table->string('name', 255); // Tên gói VPS (bắt buộc)
            $table->text('description')->nullable(); // Mô tả gói VPS (có thể null)
            $table->integer('price_month'); // Giá thuê theo tháng (bắt buộc)
            $table->integer('price_year'); // Giá thuê theo năm (bắt buộc)
            $table->text('specs')->nullable(); // Thông số kỹ thuật của gói VPS (có thể null)
            $table->string('image', 500)->nullable(); // Đường dẫn ảnh preview (có thể null)
            $table->string('time', 500)->nullable(); // Thời gian thêm gói VPS (có thể null)
        });
    }

    /**
     * Rollback migration - xóa bảng listvps
     * 
     * @return void
     */
    public function down(): void
    {
        // Xóa bảng listvps nếu tồn tại
        Schema::dropIfExists('listvps');
    }
};

