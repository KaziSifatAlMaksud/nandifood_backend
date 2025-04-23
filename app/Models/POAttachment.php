<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POAttachment extends Model
{
    use HasFactory;

    protected $table = 'po_attachments';
        public $timestamps = false;

    protected $fillable = [
        'po_id',
        'file_description',
        'note_date',
        'file_path',
        'created_at',
        'created_by',
        'uploaded_by',
        'type',
    ];

    // Define relationship with PO
    public function po()
    {
        return $this->belongsTo(PO::class, 'po_id');
    }
}
