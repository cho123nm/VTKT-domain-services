<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceCode extends Model
{
    protected $table = 'listsourcecode';
    
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'description',
        'price',
        'file_path',
        'download_link',
        'image',
        'category',
        'time'
    ];

    /**
     * Relationship với SourceCodeHistory
     */
    public function histories()
    {
        return $this->hasMany(SourceCodeHistory::class, 'source_code_id');
    }
}

