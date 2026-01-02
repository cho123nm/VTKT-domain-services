<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingHistory extends Model
{
    protected $table = 'hostinghistory';
    
    public $timestamps = false;
    
    protected $fillable = [
        'uid',
        'hosting_id',
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
     * Relationship với Hosting
     */
    public function hosting()
    {
        return $this->belongsTo(Hosting::class, 'hosting_id');
    }
}
