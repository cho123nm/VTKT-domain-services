<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VPS extends Model
{
    protected $table = 'listvps';
    
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'description',
        'price_month',
        'price_year',
        'specs',
        'image',
        'time'
    ];
}

