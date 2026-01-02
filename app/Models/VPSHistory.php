<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VPSHistory extends Model
{
    protected $table = 'vpshistory';
    
    public $timestamps = false;
    
    protected $fillable = [
        'uid',
        'vps_id',
        'period',
        'mgd',
        'status',
        'time'
    ];

    /**
     * Relationship với User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    /**
     * Relationship với VPS
     */
    public function vps()
    {
        return $this->belongsTo(VPS::class, 'vps_id');
    }
}
