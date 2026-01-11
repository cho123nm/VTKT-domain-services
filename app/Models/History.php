<?php
// Khai báo namespace cho Model này - thuộc App\Models
namespace App\Models;

// Import Eloquent Model base class
use Illuminate\Database\Eloquent\Model;

/**
 * Class History
 * Model quản lý lịch sử mua domain trong hệ thống
 * Kế thừa từ Eloquent Model để có các tính năng ORM của Laravel
 */
class History extends Model
{
    // Tên bảng trong database
    protected $table = 'history';
    
    // Tắt tự động quản lý timestamps (created_at, updated_at)
    // Vì bảng history không có các cột này, chỉ có cột 'time'
    public $timestamps = false;
    
    // Các cột có thể được mass assignment (gán hàng loạt)
    protected $fillable = [
        'uid', // User ID - ID người dùng mua domain
        'domain', // Tên domain đã mua
        'ns1', // Nameserver 1
        'ns2', // Nameserver 2
        'hsd', // Hạn sử dụng (tháng)
        'status', // Trạng thái đơn hàng (0=Chờ xử lý, 1=Đã duyệt, 2=Đã hủy, 3=Hoàn thành, 4=Từ chối)
        'mgd', // Mã giao dịch
        'time', // Thời gian tạo đơn hàng
        'timedns', // Thời gian DNS (dùng để tracking)
        'ahihi' // Trường đặc biệt (có thể dùng để đánh dấu)
    ];

    /**
     * Relationship với User
     * Một đơn hàng domain thuộc về một user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // belongsTo: một History thuộc về một User
        // 'uid' là foreign key trong bảng history trỏ đến 'id' trong bảng users
        return $this->belongsTo(User::class, 'uid');
    }

    /**
     * Đếm số đơn hàng theo trạng thái
     * Static method - có thể gọi trực tiếp từ class
     * 
     * @param int $status - Trạng thái cần đếm
     * @return int - Số lượng đơn hàng có trạng thái đó
     */
    public static function countByStatus(int $status): int
    {
        // Đếm số đơn hàng có status khớp
        return self::where('status', $status)->count();
    }

    /**
     * Đếm số đơn hàng có ahihi = 1
     * Static method - có thể gọi trực tiếp từ class
     * 
     * @return int - Số lượng đơn hàng có ahihi = 1
     */
    public static function countAhihiOne(): int
    {
        // Đếm số đơn hàng có ahihi = 1
        return self::where('ahihi', 1)->count();
    }

    /**
     * Tìm đơn hàng theo timedns
     * Static method - có thể gọi trực tiếp từ class
     * 
     * @param string $timedns - Giá trị timedns cần tìm
     * @return self|null - Trả về History instance nếu tìm thấy, null nếu không
     */
    public static function getByTimedns(string $timedns): ?self
    {
        // Tìm đơn hàng đầu tiên với timedns khớp
        return self::where('timedns', $timedns)->first();
    }

    /**
     * Reset timedns về '0'
     * 
     * @return bool - true nếu lưu thành công, false nếu không
     */
    public function resetTimedns(): bool
    {
        // Gán timedns = '0'
        $this->timedns = '0';
        // Lưu vào database và trả về kết quả
        return $this->save();
    }
}

