<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingInfo extends Model
{
    use HasFactory;
    protected $table = 'shipping_info';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false; 
    protected $fillable = [
        'select_shipping_location',
        'address1',
        'address2',
        'city',
        'state',
        'zip_code',
        'country',
        'email',
        'phone',
        'mobile',
        'first_name',
        'middle_name',
        'last_name',
        'position_id',
        'type',
    ];
}
