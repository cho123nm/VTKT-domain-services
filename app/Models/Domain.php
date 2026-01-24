<?php
// Khai báo namespace cho Model này - thuộc App\Models
namespace App\Models;

// Import Eloquent Model base class
use Illuminate\Database\Eloquent\Model;

/**
 * Class Domain
 * Model quản lý thông tin các loại domain (đuôi miền) trong hệ thống
 * Kế thừa từ Eloquent Model để có các tính năng ORM của Laravel
 */
class Domain extends Model
{
    // Tên bảng trong database (khác với tên class)
    protected $table = 'listdomain';
    
    // Tắt timestamps vì bảng không có cột created_at và updated_at
    public $timestamps = false;
    
    // Các cột có thể được mass assignment (gán hàng loạt)
    protected $fillable = [
        'image', // Đường dẫn ảnh đại diện cho loại domain
        'price', // Giá domain
        'duoi' // Đuôi domain (ví dụ: .com, .vn, .net)
    ];

    /**
     * Tìm domain theo đuôi
     * Static method - có thể gọi trực tiếp từ class mà không cần instance
     * 
     * @param string $duoi - Đuôi domain (ví dụ: '.com')
     * @return self|null - Trả về Domain instance nếu tìm thấy, null nếu không
     */
    public static function findByDuoi(string $duoi): ?self
    {
        // Tìm domain đầu tiên trong database với đuôi khớp
        return self::where('duoi', $duoi)->first();
    }
}

