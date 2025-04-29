<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgnItemsDetail extends Model
{
    protected $table = 'rgn_items_details';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'rgn_id',
        'sku',
        'product_name',
        'size',
        'uom',
        'batch_no',
        'expiration_date',
        'returned_qty',
        'qty_received',
        'qty_varience',
        'unit_cost',
        'total_amount',
        'status',
        'comment',
        'created_at',
        'updated_at',
    ];
}
