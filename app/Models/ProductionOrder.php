<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $table = 'production_orders';
    protected $primaryKey = 'id';
    public $timestamps = false; 
    protected $fillable = [
        'production_order_no',
        'prd_id',
        'type',
        'production_date',
        'product_category',
        'sub_category1',
        'sub_category2',
        'output_product',
        'input_item',
        'sku',
        'size',
        'uom',
        'qty',
        'currency',
        'unit_cost',
        'amount',
    ];
}
