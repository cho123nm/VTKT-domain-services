<?php
// Khai báo namespace cho Model này - thuộc App\Models
namespace App\Models;

// Import Eloquent Model base class
use Illuminate\Database\Eloquent\Model;

/**
 * Class Card
 * Model quản lý thông tin thẻ cào trong hệ thống
 * Kế thừa từ Eloquent Model để có các tính năng ORM của Laravel
 */
class Card extends Model
{
    // Tên bảng trong database
    protected $table = 'cards';
    
    // Các cột có thể được mass assignment (gán hàng loạt)
    protected $fillable = [
        'uid', // User ID - ID người dùng nạp thẻ
        'pin', // Mã PIN thẻ cào
        'serial', // Serial thẻ cào
        'type', // Loại thẻ (VIETTEL, VINAPHONE, etc.)
        'amount', // Mệnh giá thẻ
        'requestid', // Request ID từ API thẻ cào
        'status', // Trạng thái (0=Chờ xử lý, 1=Thành công, 2=Thất bại)
        'time', // Thời gian tạo
        'time2', // Thời gian xử lý (có thể dùng để thống kê theo ngày)
        'time3' // Thời gian hoàn thành (có thể dùng để thống kê theo ngày)
    ];

    /**
     * Relationship với User
     * Một thẻ cào thuộc về một user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // belongsTo: một Card thuộc về một User
        // 'uid' là foreign key trong bảng cards trỏ đến 'id' trong bảng users
        return $this->belongsTo(User::class, 'uid');
    }

    /**
     * Tính tổng mệnh giá thẻ theo trạng thái và time2
     * Static method - có thể gọi trực tiếp từ class
     * 
     * @param int $status - Trạng thái cần tính tổng
     * @param string $time2 - Giá trị time2 cần tính tổng
     * @return int - Tổng mệnh giá
     */
    public static function sumAmountByStatusAndTime2(int $status, string $time2): int
    {
        // Tính tổng mệnh giá các thẻ có status và time2 khớp, ép kiểu về int
        return (int)self::where('status', $status)
            ->where('time2', $time2)
            ->sum('amount');
    }

    /**
     * Tính tổng mệnh giá thẻ theo trạng thái và time3
     * Static method - có thể gọi trực tiếp từ class
     * 
     * @param int $status - Trạng thái cần tính tổng
     * @param string $time3 - Giá trị time3 cần tính tổng
     * @return int - Tổng mệnh giá
     */
    public static function sumAmountByStatusAndTime3(int $status, string $time3): int
    {
        // Tính tổng mệnh giá các thẻ có status và time3 khớp, ép kiểu về int
        return (int)self::where('status', $status)
            ->where('time3', $time3)
            ->sum('amount');
    }

    /**
     * Tính tổng mệnh giá thẻ theo trạng thái
     * Static method - có thể gọi trực tiếp từ class
     * 
     * @param int $status - Trạng thái cần tính tổng
     * @return int - Tổng mệnh giá
     */
    public static function sumAmountByStatus(int $status): int
    {
        // Tính tổng mệnh giá các thẻ có status khớp, ép kiểu về int
        return (int)self::where('status', $status)->sum('amount');
    }
}

