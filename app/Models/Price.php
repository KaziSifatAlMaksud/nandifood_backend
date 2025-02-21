<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $table = 'price'; // Ensure this matches your database table name
    public $timestamps = false; 
    protected $fillable = [
        'price',
        'no',
        'country',
        'state',
        'city',
        'warehouse',
        'sku',
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
        'on_hand_qty_sales_uom1',
        'sales_uom2',
        'on_hand_qty_sales_uom2',
        'sales_uom3',
        'on_hand_qty_sales_uom3',
    ];
}
