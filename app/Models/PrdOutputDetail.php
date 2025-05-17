<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrdOutputDetail extends Model
{
    use HasFactory;

    protected $table = 'prd_output_details';
    protected $primaryKey = 'id'; 
    protected $fillable = [
        'production_order_no',
        'prd_id',
        'production_date',
        'product_category',
        'sub_category1',
        'sub_category2',
        'output_item',
        'sku',
        'size',
        'uom',
        'qty',
        'currency',
        'unit_cost',
        'amount',
    ];
}
