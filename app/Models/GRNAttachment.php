<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GRNAttachment extends Model
{
    use HasFactory;

    protected $table = 'grn_attachments';

    protected $fillable = [
        'grn_id',
        'file_description',
        'note_date',
        'file_path',
        'uploaded_by',
        'type',
    ];

    // Define relationship with GRN
    public function grn()
    {
        return $this->belongsTo(GRN::class, 'grn_id');
    }
}
