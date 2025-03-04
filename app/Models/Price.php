<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $table = 'price'; 
    public $timestamps = false;
    protected $primaryKey = 'id'; 
    protected $fillable = [
        'excel_id',
        'no',
        'country',
        'state',
        'city',
        'warehouse',
        'sku',
        'upc',
        'product_name',
        'category',
        'sub_category1',
        'sub_category2',
        'inventory_uom',
        'size',
        'product_weight_in_lb',
        'product_weight_kg',
        'on_hand_qty_inventory_uom',
        'sales_uom1',
        'currency',
        'price_sales_uom1',
        'sales_uom2',
        'price_sales_uom2',
        'sales_uom3',
        'price_sales_uom3',
    ];
}
