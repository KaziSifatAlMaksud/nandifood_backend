<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GTNAttachment extends Model
{
    use HasFactory;

    protected $table = 'gtn_attachments';

    protected $fillable = [
        'gtn_id',
        'file_description',
        'note_date',
        'file_path',
        'uploaded_by',
        'type',
    ];

    // Define relationship with GTN
    public function gtn()
    {
        return $this->belongsTo(GTN::class, 'gtn_id');
    }
}
