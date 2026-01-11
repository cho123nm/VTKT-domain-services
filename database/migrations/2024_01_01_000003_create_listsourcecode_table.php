<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Database\Migrations\Migration; // Base class cho migration
use Illuminate\Database\Schema\Blueprint; // Class để định nghĩa cấu trúc bảng
use Illuminate\Support\Facades\Schema; // Facade để thao tác với database schema

/**
 * Migration tạo bảng listsourcecode (danh sách source code)
 * Bảng này lưu trữ thông tin các source code được bán trên hệ thống
 */
return new class extends Migration
{
    /**
     * Chạy migration - tạo bảng listsourcecode
     * 
     * @return void
     */
    public function up(): void
    {
        // Tạo bảng listsourcecode
        Schema::create('listsourcecode', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng (primary key)
            $table->string('name', 255); // Tên source code (bắt buộc)
            $table->text('description')->nullable(); // Mô tả source code (có thể null)
            $table->integer('price'); // Giá source code (bắt buộc)
            $table->string('file_path', 500)->nullable(); // Đường dẫn file source code trong storage (có thể null)
            $table->string('download_link', 500)->nullable(); // Link download từ bên ngoài (có thể null)
            $table->string('image', 500)->nullable(); // Đường dẫn ảnh preview (có thể null)
            $table->string('category', 100)->nullable(); // Danh mục source code (có thể null)
            $table->string('time', 50)->nullable(); // Thời gian thêm source code (có thể null)
        });
    }

    /**
     * Rollback migration - xóa bảng listsourcecode
     * 
     * @return void
     */
    public function down(): void
    {
        // Xóa bảng listsourcecode nếu tồn tại
        Schema::dropIfExists('listsourcecode');
    }
};

