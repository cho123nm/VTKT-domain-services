<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Database\Migrations\Migration; // Base class cho migration
use Illuminate\Database\Schema\Blueprint; // Class để định nghĩa cấu trúc bảng
use Illuminate\Support\Facades\Schema; // Facade để thao tác với database schema

/**
 * Migration tạo bảng history (lịch sử mua domain)
 * Bảng này lưu trữ lịch sử các đơn hàng mua domain của users
 */
return new class extends Migration
{
    /**
     * Chạy migration - tạo bảng history
     * 
     * @return void
     */
    public function up(): void
    {
        // Tạo bảng history
        Schema::create('history', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng (primary key)
            $table->unsignedBigInteger('uid')->nullable(); // ID người dùng (foreign key đến users.id) (có thể null)
            $table->string('domain', 255)->nullable(); // Tên domain đầy đủ (ví dụ: example.com) (có thể null)
            $table->string('ns1', 255)->nullable(); // Nameserver 1 (có thể null)
            $table->string('ns2', 255)->nullable(); // Nameserver 2 (có thể null)
            $table->integer('hsd')->nullable(); // Hạn sử dụng (số năm) (có thể null)
            $table->integer('status')->nullable(); // Trạng thái đơn hàng: 0 = Chờ xử lý, 1 = Đã duyệt, 2 = Đang xử lý, 3 = Hoàn thành, 4 = Từ chối (có thể null)
            $table->string('mgd', 255)->nullable(); // Mã giao dịch (MGD) - dùng để theo dõi đơn hàng (có thể null)
            $table->string('time', 255)->nullable(); // Thời gian mua domain (có thể null)
            $table->string('timedns', 255)->nullable(); // Thời gian cập nhật DNS (có thể null)
            $table->integer('ahihi')->nullable(); // Flag yêu cầu cập nhật DNS: 0 = Không, 1 = Có yêu cầu (có thể null)
            $table->index('uid'); // Tạo index cho cột uid để tìm kiếm nhanh hơn
            $table->index('domain'); // Tạo index cho cột domain để tìm kiếm nhanh hơn
            $table->index('status'); // Tạo index cho cột status để filter nhanh hơn
            $table->index('mgd'); // Tạo index cho cột mgd để tìm kiếm nhanh hơn
            // Tạo foreign key constraint: uid tham chiếu đến users.id
            // onDelete('cascade'): khi xóa user, tự động xóa các đơn hàng của user đó
            // onUpdate('cascade'): khi cập nhật user.id, tự động cập nhật uid trong các đơn hàng
            $table->foreign('uid')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Rollback migration - xóa bảng history
     * 
     * @return void
     */
    public function down(): void
    {
        // Xóa bảng history nếu tồn tại
        Schema::dropIfExists('history');
    }
};

