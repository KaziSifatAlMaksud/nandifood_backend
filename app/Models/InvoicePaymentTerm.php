<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePaymentTerm extends Model
{
    use HasFactory;
    protected $table = 'invoice_payment_terms';

    protected $primaryKey = 'id'; 

    // Define the fields that are mass assignable
    protected $fillable = [
        'payment_id',
        'payment_terms_name',
        'number_of_days_from',
        'days',
    ];
    public $timestamps = false; // Set to false if you don't need timestamps
}
