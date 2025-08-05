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
        'customer_billing_address1',
        'customer_billing_address2',
        'customer_billing_city',
        'customer_billing_state',
        'customer_billing_country',
        'customer_billing_zip',
        'customer_billing_phone',
        'customer_billing_email',
        'customer_shipping_address1',
        'customer_shipping_address2',
        'customer_shipping_city',
        'customer_shipping_state',
        'customer_shipping_country',
        'customer_shipping_zip',
        'customer_shipping_phone',
        'customer_shipping_email',
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


    public function details()
    {
        return $this->hasMany(SalesInvoiceDetailsAmountInfo::class, 'sales_invoice_id');
    }

}


