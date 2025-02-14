<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierNote extends Model
{
     use HasFactory;

    protected $table = 'supplier_notes';

    protected $primaryKey = 'id';


    public $timestamps = false;

    protected $fillable = [
        'supplier_id',
        'file_description',
        'note_date',
        'file_path',
        'uploaded_by',
    ];

    // public function supplier()
    // {
    //    return $this->belongsTo(Supplier::class, 'supplier_no', 'id');
    // }
}
