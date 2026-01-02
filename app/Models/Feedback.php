<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';
    
    // Disable timestamps vì bảng đã có cột 'time' riêng
    public $timestamps = false;
    
    protected $fillable = [
        'uid',
        'username',
        'email',
        'message',
        'admin_reply',
        'status',
        'telegram_chat_id',
        'time',
        'reply_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}

