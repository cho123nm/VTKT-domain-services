<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $table = 'cards';
    
    protected $fillable = [
        'uid',
        'pin',
        'serial',
        'type',
        'amount',
        'requestid',
        'status',
        'time',
        'time2',
        'time3'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public static function sumAmountByStatusAndTime2(int $status, string $time2): int
    {
        return (int)self::where('status', $status)
            ->where('time2', $time2)
            ->sum('amount');
    }

    public static function sumAmountByStatusAndTime3(int $status, string $time3): int
    {
        return (int)self::where('status', $status)
            ->where('time3', $time3)
            ->sum('amount');
    }

    public static function sumAmountByStatus(int $status): int
    {
        return (int)self::where('status', $status)->sum('amount');
    }
}

