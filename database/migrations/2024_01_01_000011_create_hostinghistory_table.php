<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Database\Migrations\Migration; // Base class cho migration
use Illuminate\Database\Schema\Blueprint; // Class để định nghĩa cấu trúc bảng
use Illuminate\Support\Facades\Schema; // Facade để thao tác với database schema

/**
 * Migration tạo bảng hostinghistory (lịch sử mua hosting)
 * Bảng này lưu trữ lịch sử các đơn hàng mua hosting của users
 */
return new class extends Migration
{
    /**
     * Chạy migration - tạo bảng hostinghistory
     * 
     * @return void
     */
    public function up(): void
    {
        // Tạo bảng hostinghistory
        Schema::create('hostinghistory', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng (primary key)
            $table->unsignedBigInteger('uid'); // ID người dùng (foreign key đến users.id) (bắt buộc)
            $table->unsignedBigInteger('hosting_id'); // ID gói hosting (foreign key đến listhosting.id) (bắt buộc)
            $table->string('period', 20); // Thời hạn thuê: 'month' hoặc 'year' (bắt buộc)
            $table->string('mgd', 100); // Mã giao dịch (MGD) - dùng để theo dõi đơn hàng (bắt buộc)
            $table->string('time', 50)->nullable(); // Thời gian mua hosting (có thể null)
            $table->integer('status')->default(0); // Trạng thái đơn hàng: 0 = Chờ xử lý, 1 = Đã duyệt, 2 = Đang xử lý, 3 = Hoàn thành, 4 = Từ chối (mặc định: 0)
            $table->index('uid'); // Tạo index cho cột uid để tìm kiếm nhanh hơn
            $table->index('hosting_id'); // Tạo index cho cột hosting_id để tìm kiếm nhanh hơn
            $table->index('mgd'); // Tạo index cho cột mgd để tìm kiếm nhanh hơn
        });
    }

    /**
     * Rollback migration - xóa bảng hostinghistory
     * 
     * @return void
     */
    public function down(): void
    {
        // Xóa bảng hostinghistory nếu tồn tại
        Schema::dropIfExists('hostinghistory');
    }
};

