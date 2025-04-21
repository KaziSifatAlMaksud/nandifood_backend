<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnReceivingDetail extends Model
{
    protected $table = 'grn_receiving_details';

    protected $fillable = [
        'grn_id',
        'supplier_sku',
        'our_sku',
        'product_name',
        'size',
        'uom',
        'batch_no',
        'expiration_date',
        'qty_order',
        'qty_received',
        'qty_variance',
        'unit_cost',
        'total_amount',
        'receive_reject_action',
        'rejection_resolution',
        'comment',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false; // since created_at and updated_at are manually stored as VARCHAR
}
