<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $table = 'listdomain';
    
    protected $fillable = [
        'image',
        'price',
        'duoi'
    ];

    /**
     * Tìm domain theo đuôi
     */
    public static function findByDuoi(string $duoi): ?self
    {
        return self::where('duoi', $duoi)->first();
    }
}

