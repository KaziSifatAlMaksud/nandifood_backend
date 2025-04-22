<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GTN extends Model
{
       use HasFactory;

    protected $table = 'gtns'; // Changing the table name

    protected $primaryKey = 'id';

    protected $fillable = [
        'grn_number',
        'transfer_out_warehouse',
        'transfer_in_warehouse',
        'date_tran_out',
        'po_id',
        'other_reference',
        'bol_number',
        'bol_date',
        'shipping_carrier',
        'delivery_driver',
        'transferred_out_by',
        'status',
        'notes',
        'last_updated_by',
        'is_approved'
    ];
    public function transferOutDetail(){
        return $this->hasMany(GtnTransferOutDetail::class, 'gtn_id');
    }
}
