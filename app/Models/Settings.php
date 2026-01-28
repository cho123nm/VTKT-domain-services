<?php
// Khai báo namespace cho Model này - thuộc App\Models
namespace App\Models;

// Import Eloquent Model base class
use Illuminate\Database\Eloquent\Model;

/**
 * Class Settings
 * Model quản lý cài đặt chung của hệ thống
 * Thường chỉ có 1 record trong bảng này
 * Kế thừa từ Eloquent Model để có các tính năng ORM của Laravel
 */
class Settings extends Model
{
    // Tên bảng trong database (khác với tên class)
    protected $table = 'caidatchung';
    
    // Tắt tự động quản lý timestamps (created_at, updated_at)
    public $timestamps = false;
    
    // Các cột có thể được mass assignment (gán hàng loạt)
    protected $fillable = [
        'tieude', // Tiêu đề website
        'theme', // Theme/giao diện website
        'keywords', // Từ khóa SEO
        'mota', // Mô tả website
        'imagebanner', // Ảnh banner
        'sodienthoai', // Số điện thoại liên hệ
        'banner', // Banner
        'logo', // Logo website
        'webgach', // Web gạch (có thể là link hoặc text) - Cũ, giữ lại để tương thích
        'apikey', // API key (có thể cho các dịch vụ tích hợp) - Cũ, giữ lại để tương thích
        'callback', // Callback URL - Cũ, giữ lại để tương thích
        'cardvip_partner_id', // Partner ID từ CardVIP (mới)
        'cardvip_partner_key', // Partner Key từ CardVIP (mới)
        'cardvip_api_url', // API URL CardVIP (mới)
        'cardvip_callback', // Callback URL CardVIP (mới)
        'facebook_link', // Link Facebook
        'zalo_phone', // Số điện thoại Zalo
        'telegram_bot_token', // Telegram bot token
        'telegram_admin_chat_id' // Telegram admin chat ID
    ];

    /**
     * Lấy settings đầu tiên (thường chỉ có 1 record)
     * Static method - có thể gọi trực tiếp từ class
     * 
     * @return self|null - Trả về Settings instance nếu tìm thấy, null nếu không
     */
    public static function getOne(): ?self
    {
        // Lấy record đầu tiên trong bảng (thường chỉ có 1 record)
        return self::first();
    }
}

