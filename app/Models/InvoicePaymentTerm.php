<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePaymentTerm extends Model
{
    use HasFactory;

    // Define the table name (if it's not the default plural of the model name)
    protected $table = 'invoice_payment_terms';

    // Define the primary key column if it's not 'id' (optional)
    protected $primaryKey = 'id'; // Optional if using the default 'id'

    // Define the fields that are mass assignable
    protected $fillable = [
        'payment_terms_name',
        'number_of_days_from',
        'days',
    ];

    // Optionally, disable timestamps if you don't want 'created_at' and 'updated_at' columns
    public $timestamps = false; // Set to false if you don't need timestamps
}
