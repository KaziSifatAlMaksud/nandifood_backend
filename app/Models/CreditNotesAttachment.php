<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditNotesAttachment extends Model
{
    use HasFactory;

    // If your table name does not follow Laravel's pluralization convention
    protected $table = 'credit_notes_attachment';

    // Define the fillable fields
    protected $fillable = [
        'credit_id',
        'file_description',
        'note_date',
        'file_path',
        'uploaded_by',
    ];

    // If your table does NOT have created_at and updated_at columns
    public $timestamps = false;


}
