<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DGNAttachment extends Model
{
    use HasFactory;

    protected $table = 'dgn_attachments';

    protected $fillable = [
        'dgn_id',
        'file_description',
        'uploaded_by',
        'file_path',
        'uploaded_at',
    ];

    // Define relationship with DGN
    public function dgn()
    {
        return $this->belongsTo(DGN::class, 'dgn_id');
    }
}
