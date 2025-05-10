<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PutAwayDetail extends Model
{
    use HasFactory;

    protected $table = 'put_away_details';
    protected $primaryKey = 'id';
    public $incrementing = true;
    
    protected $fillable = [
        'grn_id',
        'sku',
        'product_name',
        'size',
        'uom',
        'batch_no',
        'exp_date',
        'qty_rec',
        'qty_put_away',
        'qty_varience',
        'pu',
        'pu_count',
        'hu',
        'hu_count',
        'req_storage',
        'bin_location_id',
        'avilable_storage',
        'aviable_storage2',
        'put_away_status',
        'comment',
    ];

}
