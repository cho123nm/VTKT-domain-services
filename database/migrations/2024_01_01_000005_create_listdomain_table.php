<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Database\Migrations\Migration; // Base class cho migration
use Illuminate\Database\Schema\Blueprint; // Class để định nghĩa cấu trúc bảng
use Illuminate\Support\Facades\Schema; // Facade để thao tác với database schema

/**
 * Migration tạo bảng listdomain (danh sách đuôi domain)
 * Bảng này lưu trữ thông tin các đuôi domain được hỗ trợ và giá của chúng
 */
return new class extends Migration
{
    /**
     * Chạy migration - tạo bảng listdomain
     * 
     * @return void
     */
    public function up(): void
    {
        // Tạo bảng listdomain
        Schema::create('listdomain', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng (primary key)
            $table->string('image', 2655)->nullable(); // Đường dẫn ảnh đại diện cho đuôi domain (có thể null)
            $table->string('price', 2555)->nullable(); // Giá đăng ký domain với đuôi này (có thể null)
            $table->string('duoi', 255)->nullable(); // Đuôi domain (ví dụ: .com, .vn, .net) (có thể null)
            $table->index('duoi'); // Tạo index cho cột duoi để tìm kiếm nhanh hơn
        });
    }

    /**
     * Rollback migration - xóa bảng listdomain
     * 
     * @return void
     */
    public function down(): void
    {
        // Xóa bảng listdomain nếu tồn tại
        Schema::dropIfExists('listdomain');
    }
};

