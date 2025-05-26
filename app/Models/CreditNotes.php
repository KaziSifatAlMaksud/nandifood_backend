<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditNotes extends Model
{
    use HasFactory;

    protected $table = 'credit_note';
    public $timestamps = false;

    protected $fillable = [
        'sales_invoice_no',
        'sales_invoice_date',
        'shiping_ticket_no',
        'date_shipped',
        'sales_invoice_status',
        'sales_rep',
        'credit_note_date',
        'credit_note_no',
        'customer',
        'customer_billing_location',
        'customer_shop_no',
        'customer_email',
        'customer_phone',
        'customer_contact',
        'grn_no',
        'grn_date',
        'date_created',
        'date_approved',
        'date_applied',
        'applied_sales_invoice',
        'applied_date',
        'refund_note_no',
        'refund_date',
        'last_updated',
        'last_updated_by',
        'status',
        'is_approved',
        'notes',
    ];

    
}
