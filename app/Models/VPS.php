<?php
// Khai báo namespace cho Model này - thuộc App\Models
namespace App\Models;

// Import Eloquent Model base class
use Illuminate\Database\Eloquent\Model;

/**
 * Class VPS
 * Model quản lý thông tin các gói VPS trong hệ thống
 * Kế thừa từ Eloquent Model để có các tính năng ORM của Laravel
 */
class VPS extends Model
{
    // Tên bảng trong database (khác với tên class)
    protected $table = 'listvps';
    
    // Tắt tự động quản lý timestamps (created_at, updated_at)
    // Vì bảng listvps không có các cột này, chỉ có cột 'time'
    public $timestamps = false;
    
    // Các cột có thể được mass assignment (gán hàng loạt)
    protected $fillable = [
        'name', // Tên gói VPS
        'description', // Mô tả gói VPS
        'price_month', // Giá theo tháng
        'price_year', // Giá theo năm
        'specs', // Thông số kỹ thuật (có thể là JSON hoặc text)
        'image', // Đường dẫn ảnh đại diện cho gói VPS
        'time' // Thời gian tạo gói VPS
    ];
}

