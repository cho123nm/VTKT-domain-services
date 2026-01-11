<?php
// Khai báo namespace cho Model này - thuộc App\Models
namespace App\Models;

// Import Eloquent Model base class
use Illuminate\Database\Eloquent\Model;

/**
 * Class VPSHistory
 * Model quản lý lịch sử mua VPS trong hệ thống
 * Kế thừa từ Eloquent Model để có các tính năng ORM của Laravel
 */
class VPSHistory extends Model
{
    // Tên bảng trong database
    protected $table = 'vpshistory';
    
    // Tắt tự động quản lý timestamps (created_at, updated_at)
    // Vì bảng vpshistory không có các cột này, chỉ có cột 'time'
    public $timestamps = false;
    
    // Các cột có thể được mass assignment (gán hàng loạt)
    protected $fillable = [
        'uid', // User ID - ID người dùng mua VPS
        'vps_id', // VPS ID - ID gói VPS đã mua
        'period', // Thời hạn: 'month' hoặc 'year'
        'mgd', // Mã giao dịch
        'status', // Trạng thái đơn hàng (0=Chờ xử lý, 1=Đã duyệt, 2=Đã hủy)
        'time' // Thời gian tạo đơn hàng
    ];

    /**
     * Relationship với User
     * Một đơn hàng VPS thuộc về một user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // belongsTo: một VPSHistory thuộc về một User
        // 'uid' là foreign key trong bảng vpshistory trỏ đến 'id' trong bảng users
        return $this->belongsTo(User::class, 'uid');
    }

    /**
     * Relationship với VPS
     * Một đơn hàng VPS thuộc về một gói VPS
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vps()
    {
        // belongsTo: một VPSHistory thuộc về một VPS
        // 'vps_id' là foreign key trong bảng vpshistory trỏ đến 'id' trong bảng listvps
        return $this->belongsTo(VPS::class, 'vps_id');
    }
}
