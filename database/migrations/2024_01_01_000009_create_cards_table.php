<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Database\Migrations\Migration; // Base class cho migration
use Illuminate\Database\Schema\Blueprint; // Class để định nghĩa cấu trúc bảng
use Illuminate\Support\Facades\Schema; // Facade để thao tác với database schema

/**
 * Migration tạo bảng cards (thẻ cào)
 * Bảng này lưu trữ thông tin các thẻ cào được nạp vào hệ thống
 */
return new class extends Migration
{
    /**
     * Chạy migration - tạo bảng cards
     * 
     * @return void
     */
    public function up(): void
    {
        // Tạo bảng cards
        Schema::create('cards', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng (primary key)
            $table->unsignedBigInteger('uid')->nullable(); // ID người dùng nạp thẻ (foreign key đến users.id) (có thể null)
            $table->string('pin', 255)->nullable(); // Mã PIN thẻ cào (có thể null)
            $table->string('serial', 255)->nullable(); // Serial thẻ cào (có thể null)
            $table->string('type', 255)->nullable(); // Loại thẻ: VIETTEL, VINAPHONE, MOBIFONE, etc. (có thể null)
            $table->string('amount', 255)->nullable(); // Mệnh giá thẻ (có thể null)
            $table->string('requestid', 255)->nullable(); // Request ID từ cổng nạp thẻ (có thể null)
            $table->integer('status')->nullable(); // Trạng thái thẻ: 0 = Đang duyệt, 1 = Thẻ đúng, 2 = Thẻ sai (có thể null)
            $table->string('time', 255)->nullable(); // Thời gian nạp thẻ (có thể null)
            $table->string('time2', 255)->nullable(); // Thời gian nạp thẻ (format khác) (có thể null)
            $table->string('time3', 255)->nullable(); // Thời gian nạp thẻ (format khác) (có thể null)
            $table->index('uid'); // Tạo index cho cột uid để tìm kiếm nhanh hơn
            $table->index('status'); // Tạo index cho cột status để filter nhanh hơn
            $table->index('requestid'); // Tạo index cho cột requestid để tìm kiếm nhanh hơn
            // Tạo foreign key constraint: uid tham chiếu đến users.id
            // onDelete('cascade'): khi xóa user, tự động xóa các thẻ cào của user đó
            // onUpdate('cascade'): khi cập nhật user.id, tự động cập nhật uid trong các thẻ cào
            $table->foreign('uid')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Rollback migration - xóa bảng cards
     * 
     * @return void
     */
    public function down(): void
    {
        // Xóa bảng cards nếu tồn tại
        Schema::dropIfExists('cards');
    }
};

