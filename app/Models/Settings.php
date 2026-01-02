<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'caidatchung';
    public $timestamps = false;
    
    protected $fillable = [
        'tieude',
        'theme',
        'keywords',
        'mota',
        'imagebanner',
        'sodienthoai',
        'banner',
        'logo',
        'webgach',
        'apikey',
        'callback',
        'facebook_link',
        'zalo_phone',
        'telegram_bot_token',
        'telegram_admin_chat_id'
    ];

    /**
     * Lấy settings đầu tiên (thường chỉ có 1 record)
     */
    public static function getOne(): ?self
    {
        return self::first();
    }
}

