<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history';
    
    public $timestamps = false; // Bảng không có created_at và updated_at
    
    protected $fillable = [
        'uid',
        'domain',
        'ns1',
        'ns2',
        'hsd',
        'status',
        'mgd',
        'time',
        'timedns',
        'ahihi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public static function countByStatus(int $status): int
    {
        return self::where('status', $status)->count();
    }

    public static function countAhihiOne(): int
    {
        return self::where('ahihi', 1)->count();
    }

    public static function getByTimedns(string $timedns): ?self
    {
        return self::where('timedns', $timedns)->first();
    }

    public function resetTimedns(): bool
    {
        $this->timedns = '0';
        return $this->save();
    }
}

