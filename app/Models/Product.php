<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'p_sku_no',
        'p_long_name',
        'product_short_name',
        'product_category',
        'sub_category1',
        'sub_category2',
        'size',
        'default_sales_uom',
        'inventory_uom',
        'product_cert1',
        'product_cert2',
        'product_cert3',
        'product_upc',
        'default_warehouse',
        'warehouse_city',
        'warehouse_state',
        'warehouse_country',
        'product_manager',
        'eff_date',
        'end_date',
        'status',
        'last_updated',
        'last_updated_by',
        'img1',
        'upc_barcode',
    ];

    protected $casts = [
        'eff_date' => 'date',
        'end_date' => 'date',
        'last_updated' => 'datetime',
    ];

}
