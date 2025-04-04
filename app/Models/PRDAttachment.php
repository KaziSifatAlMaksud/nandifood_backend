<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PRDAttachment extends Model
{
    use HasFactory;

    protected $table = 'prd_attachments';

    protected $fillable = [
        'prd_id',
        'file_description',
        'note_date',
        'file_path',
        'uploaded_by',
        'type',
    ];
    public $timestamps = false; 
    // Define relationship with PRD
    public function prd()
    {
        return $this->belongsTo(PRD::class, 'prd_id');
    }
}
