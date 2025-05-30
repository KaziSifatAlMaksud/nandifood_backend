<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    protected $table = 'sales_invoice'; // Table name
   protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'invoice_no',
        'customer',
        'customer_billing_address',
        'customer_shipping_address',
        'customer_po',
        'reference_no',
        'invoice_date',
        'payment_terms',
        'payment_due_date',
        'invoice_currency',
        'sales_rep',
        'warehouse',
        'planned_ship_out_date',
        'last_updated',
        'last_updated_by',
        'invoice_status',
        'payment_status',
        'notes',
        'memo_notes',
        'is_approved',
    ];
}
