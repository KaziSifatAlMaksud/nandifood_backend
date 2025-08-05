<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceDetailsAmountInfo extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'sales_invoice_details_amount_info';

    // Primary key
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $fillable = [
        'sales_invoice_id',
        'product_id',
        'p_sku_no',
        'size',
        'uom',
        'on_hand_qty',
        'invoice_qty',
        'unit_cost',
        'unit_price',
        'amount',
        'discount',
        'tax_name_1',
        'tax_rate_1',
        'tax_amount_1',
        'tax_name_2',
        'tax_rate_2',
        'tax_amount_2',
        'total_amount',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships (examples)
     |--------------------------------------------------------------------------
     */

    // Each detail belongs to one sales invoice
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice ::class, 'sales_invoice_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
}
