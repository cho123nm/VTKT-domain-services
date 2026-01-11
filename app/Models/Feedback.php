<?php
// Khai báo namespace cho Model này - thuộc App\Models
namespace App\Models;

// Import Eloquent Model base class
use Illuminate\Database\Eloquent\Model;

/**
 * Class Feedback
 * Model quản lý phản hồi/liên hệ từ người dùng
 * Kế thừa từ Eloquent Model để có các tính năng ORM của Laravel
 */
class Feedback extends Model
{
    // Tên bảng trong database
    protected $table = 'feedback';
    
    // Tắt tự động quản lý timestamps (created_at, updated_at)
    // Vì bảng feedback đã có cột 'time' và 'reply_time' riêng
    public $timestamps = false;
    
    // Các cột có thể được mass assignment (gán hàng loạt)
    protected $fillable = [
        'uid', // User ID - ID người dùng gửi feedback
        'username', // Tên đăng nhập người gửi
        'email', // Email người gửi
        'message', // Nội dung phản hồi
        'admin_reply', // Phản hồi từ admin
        'status', // Trạng thái (0=Chưa xử lý, 1=Đã xử lý)
        'telegram_chat_id', // Telegram chat ID (nếu có tích hợp Telegram)
        'time', // Thời gian gửi feedback
        'reply_time' // Thời gian admin phản hồi
    ];

    /**
     * Relationship với User
     * Một feedback thuộc về một user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // belongsTo: một Feedback thuộc về một User
        // 'uid' là foreign key trong bảng feedback trỏ đến 'id' trong bảng users
        return $this->belongsTo(User::class, 'uid');
    }
}

