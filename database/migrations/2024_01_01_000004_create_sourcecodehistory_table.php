<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Database\Migrations\Migration; // Base class cho migration
use Illuminate\Database\Schema\Blueprint; // Class để định nghĩa cấu trúc bảng
use Illuminate\Support\Facades\Schema; // Facade để thao tác với database schema

/**
 * Migration tạo bảng sourcecodehistory (lịch sử mua source code)
 * Bảng này lưu trữ lịch sử các đơn hàng mua source code của users
 */
return new class extends Migration
{
    /**
     * Chạy migration - tạo bảng sourcecodehistory
     * 
     * @return void
     */
    public function up(): void
    {
        // Tạo bảng sourcecodehistory
        Schema::create('sourcecodehistory', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng (primary key)
            $table->unsignedBigInteger('uid'); // ID người dùng (foreign key đến users.id) (bắt buộc)
            $table->unsignedBigInteger('source_code_id'); // ID source code (foreign key đến listsourcecode.id) (bắt buộc)
            $table->string('mgd', 100); // Mã giao dịch (MGD) - dùng để theo dõi đơn hàng (bắt buộc)
            $table->string('time', 50)->nullable(); // Thời gian mua (có thể null)
            $table->integer('status')->default(0); // Trạng thái đơn hàng: 0 = Chờ xử lý, 1 = Đã duyệt, 2 = Đã từ chối, 3 = Hoàn thành (mặc định: 0)
            $table->index('uid'); // Tạo index cho cột uid để tìm kiếm nhanh hơn
            $table->index('source_code_id'); // Tạo index cho cột source_code_id để tìm kiếm nhanh hơn
            $table->index('mgd'); // Tạo index cho cột mgd để tìm kiếm nhanh hơn
        });
    }

    /**
     * Rollback migration - xóa bảng sourcecodehistory
     * 
     * @return void
     */
    public function down(): void
    {
        // Xóa bảng sourcecodehistory nếu tồn tại
        Schema::dropIfExists('sourcecodehistory');
    }
};

