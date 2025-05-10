<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RgnItemsDetail;

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
        'last_updated',
        'is_approve',
        'notes',
    ];

    public $timestamps = false; 
    public function rgnItemDetails()
    {
        return $this->hasMany(RgnItemsDetail::class, 'rgn_id');
    }



}
