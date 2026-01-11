<?php
// Khai báo namespace cho Model này - thuộc App\Models
namespace App\Models;

// Import Eloquent Model base class
use Illuminate\Database\Eloquent\Model;

/**
 * Class HostingHistory
 * Model quản lý lịch sử mua hosting trong hệ thống
 * Kế thừa từ Eloquent Model để có các tính năng ORM của Laravel
 */
class HostingHistory extends Model
{
    // Tên bảng trong database
    protected $table = 'hostinghistory';
    
    // Tắt tự động quản lý timestamps (created_at, updated_at)
    // Vì bảng hostinghistory không có các cột này, chỉ có cột 'time'
    public $timestamps = false;
    
    // Các cột có thể được mass assignment (gán hàng loạt)
    protected $fillable = [
        'uid', // User ID - ID người dùng mua hosting
        'hosting_id', // Hosting ID - ID gói hosting đã mua
        'period', // Thời hạn: 'month' hoặc 'year'
        'mgd', // Mã giao dịch
        'status', // Trạng thái đơn hàng (0=Chờ xử lý, 1=Đã duyệt, 2=Đã hủy)
        'time' // Thời gian tạo đơn hàng
    ];

    /**
     * Relationship với User
     * Một đơn hàng hosting thuộc về một user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // belongsTo: một HostingHistory thuộc về một User
        // 'uid' là foreign key trong bảng hostinghistory trỏ đến 'id' trong bảng users
        return $this->belongsTo(User::class, 'uid');
    }

    /**
     * Relationship với Hosting
     * Một đơn hàng hosting thuộc về một gói hosting
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hosting()
    {
        // belongsTo: một HostingHistory thuộc về một Hosting
        // 'hosting_id' là foreign key trong bảng hostinghistory trỏ đến 'id' trong bảng listhosting
        return $this->belongsTo(Hosting::class, 'hosting_id');
    }
}
