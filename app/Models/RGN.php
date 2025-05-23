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
        'rgn_date',
        'warehouse_id',
        'supplier',

        'supplier_invoice_no',
        'supplier_reference',
        'grn_no',
        'grn_date',

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
    public function rgn_item_details()
    {
        return $this->hasMany(RgnItemsDetail::class, 'rgn_id');
    }



}
