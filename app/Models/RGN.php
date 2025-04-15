<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rgn extends Model
{
    use HasFactory;

    protected $table = 'rgns';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'rgn_no',
        'date',
        'warehouse_id',
        'supplier',
        'bol_no',
        'shipping_company',
        'returned_by',
        'status',
        'total_amount',
        'last_updated_by',
        'is_approve',
        'notes',
    ];

    public $timestamps = false; 
}
