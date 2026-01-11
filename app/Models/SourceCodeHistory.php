<?php
// Khai báo namespace cho Model này - thuộc App\Models
namespace App\Models;

// Import Eloquent Model base class
use Illuminate\Database\Eloquent\Model;

/**
 * Class SourceCodeHistory
 * Model quản lý lịch sử mua source code trong hệ thống
 * Kế thừa từ Eloquent Model để có các tính năng ORM của Laravel
 */
class SourceCodeHistory extends Model
{
    // Tên bảng trong database
    protected $table = 'sourcecodehistory';
    
    // Tắt tự động quản lý timestamps (created_at, updated_at)
    // Vì bảng sourcecodehistory không có các cột này, chỉ có cột 'time'
    public $timestamps = false;
    
    // Các cột có thể được mass assignment (gán hàng loạt)
    protected $fillable = [
        'uid', // User ID - ID người dùng mua source code
        'source_code_id', // Source Code ID - ID source code đã mua
        'mgd', // Mã giao dịch
        'time', // Thời gian tạo đơn hàng
        'status' // Trạng thái đơn hàng (0=Chờ xử lý, 1=Đã duyệt, 2=Đã hủy)
    ];

    /**
     * Relationship với User
     * Một đơn hàng source code thuộc về một user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // belongsTo: một SourceCodeHistory thuộc về một User
        // 'uid' là foreign key trong bảng sourcecodehistory trỏ đến 'id' trong bảng users
        return $this->belongsTo(User::class, 'uid');
    }

    /**
     * Relationship với SourceCode
     * Một đơn hàng source code thuộc về một source code
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sourceCode()
    {
        // belongsTo: một SourceCodeHistory thuộc về một SourceCode
        // 'source_code_id' là foreign key trong bảng sourcecodehistory trỏ đến 'id' trong bảng listsourcecode
        return $this->belongsTo(SourceCode::class, 'source_code_id');
    }
}

