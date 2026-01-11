<?php
// Khai báo namespace cho Model này - thuộc App\Models
namespace App\Models;

// Import Eloquent Model base class
use Illuminate\Database\Eloquent\Model;

/**
 * Class Hosting
 * Model quản lý thông tin các gói hosting trong hệ thống
 * Kế thừa từ Eloquent Model để có các tính năng ORM của Laravel
 */
class Hosting extends Model
{
    // Tên bảng trong database (khác với tên class)
    protected $table = 'listhosting';
    
    // Tắt tự động quản lý timestamps (created_at, updated_at)
    // Vì bảng listhosting không có các cột này, chỉ có cột 'time'
    public $timestamps = false;
    
    // Các cột có thể được mass assignment (gán hàng loạt)
    protected $fillable = [
        'name', // Tên gói hosting
        'description', // Mô tả gói hosting
        'price_month', // Giá theo tháng
        'price_year', // Giá theo năm
        'specs', // Thông số kỹ thuật (có thể là JSON hoặc text)
        'image', // Đường dẫn ảnh đại diện cho gói hosting
        'time' // Thời gian tạo gói hosting
    ];
}

