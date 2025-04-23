<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class POItemDetail extends Model
{
    // Table name
    protected $table = 'po_item_details';

    // Primary key
    protected $primaryKey = 'id';

    // Timestamps are disabled (using VARCHAR for created_at)
    public $timestamps = false;

    // Mass assignable fields
    protected $fillable = [
        'po_id',
        'supplier_sku',
        'sku',
        'product_name',
        'size',
        'uom',
        'qty',
        'unit_price',
        'total_amount',
        'comment',
        'created_by',
        'created_at',
    ];

    // Optional: Define relationship with PO
    // public function po()
    // {
    //     return $this->belongsTo(PO::class, 'po_id');
    // }
}
