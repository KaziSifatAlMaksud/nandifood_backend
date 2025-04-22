<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GtnTransferOutDetail extends Model
{
    protected $table = 'gtn_transfer_out_details';

    protected $fillable = [
        'gtn_id',
        'sku',
        'product_name',
        'size',
        'uom',
        'batch_no',
        'expiration_date',
        'qty_ordered',
        'qty_transferred_out',
        'qty_variance',
        'unit_cost',
        'total_amount',
        'transfer',
        'comment',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;
}