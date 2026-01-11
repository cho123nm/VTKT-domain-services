<?php
// Khai báo namespace cho Model này - thuộc App\Models
namespace App\Models;

// Import Eloquent Model base class
use Illuminate\Database\Eloquent\Model;

/**
 * Class SourceCode
 * Model quản lý thông tin các source code trong hệ thống
 * Kế thừa từ Eloquent Model để có các tính năng ORM của Laravel
 */
class SourceCode extends Model
{
    // Tên bảng trong database (khác với tên class)
    protected $table = 'listsourcecode';
    
    // Tắt tự động quản lý timestamps (created_at, updated_at)
    // Vì bảng listsourcecode không có các cột này, chỉ có cột 'time'
    public $timestamps = false;
    
    // Các cột có thể được mass assignment (gán hàng loạt)
    protected $fillable = [
        'name', // Tên source code
        'description', // Mô tả source code
        'price', // Giá source code
        'file_path', // Đường dẫn file source code trong storage
        'download_link', // Link download (nếu có)
        'image', // Đường dẫn ảnh đại diện cho source code
        'category', // Danh mục source code
        'time' // Thời gian tạo source code
    ];

    /**
     * Relationship với SourceCodeHistory
     * Một source code có thể có nhiều đơn hàng
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histories()
    {
        // hasMany: một SourceCode có nhiều SourceCodeHistory (đơn hàng)
        // 'source_code_id' là foreign key trong bảng SourceCodeHistory trỏ đến 'id' trong bảng SourceCode
        return $this->hasMany(SourceCodeHistory::class, 'source_code_id');
    }
}

