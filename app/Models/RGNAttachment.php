<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RGNAttachment extends Model
{
    use HasFactory;

    protected $table = 'rgn_attachments';  
    protected $fillable = [
        'rgn_id',           
        'file_description', 
        'note_date',        
        'file_path',       
        'uploaded_by',     
        'type',  
        'updated_at',
        'created_at'          
    ];
}
