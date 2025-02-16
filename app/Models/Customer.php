<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'customer_no',
        'customer_legal_name',
        'customer_trade_name',
        'address1',
        'address2',
        'country',
        'state',
        'city',
        'zip_code',
        'email',
        'phone',
        'mobile',
        'first_name',
        'middle_name',
        'last_name',
        'position',
        'customer_category',
        'account_manager',
        'category_manager',
        'eff_date',
        'credit_terms',
        'last_updated',
        'last_updated_by',
        'status',
        'img',
        'notes',
        'is_approved',
    ];
}