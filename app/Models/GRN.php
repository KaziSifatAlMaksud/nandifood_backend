<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GrnReceivingDetail;
class GRN extends Model
{
    use HasFactory;

    protected $table = 'grns'; 

    protected $fillable = [
        'receiving_warehouse_id',
        'date_received',
        'grn_no',
        'our_po',
        'shipping_carrier',
        'supplier_shipping_address',
        'bol_date',
        'delivery_driver',
        'received_by',
        'other_reference',
        'last_updated',
        'last_updated_by',
        'status',
        'grn_notes',
        'is_approve',
        'received_details',
        'bol_number',
        'supplier_invoice_no',
        'supplier',
        'other_reference'
    ];
     public $timestamps = false;

         public function receivingDetails()
    {
        return $this->hasMany(GrnReceivingDetail::class, 'grn_id');
    }

    public function putAwayDetails()
    {
        return $this->hasMany(\App\Models\PutAwayDetail::class, 'grn_id', 'id');
    }


    // protected $casts = [
    //     'date_received' => 'date',
    //     'bol_date' => 'date',
    //     'last_updated' => 'datetime',
    //     'received_details' => 'array',
    //     'is_approve' => 'boolean',
    // ];
}
