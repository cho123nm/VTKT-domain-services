<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceCodeHistory extends Model
{
    protected $table = 'sourcecodehistory';
    
    public $timestamps = false; // Bảng không có created_at và updated_at
    
    protected $fillable = [
        'uid',
        'source_code_id',
        'mgd',
        'time',
        'status'
    ];

    /**
     * Relationship với User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    /**
     * Relationship với SourceCode
     */
    public function sourceCode()
    {
        return $this->belongsTo(SourceCode::class, 'source_code_id');
    }
}

