<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hosting extends Model
{
    protected $table = 'listhosting';
    
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

