<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class DgnDamageDetail extends Model
{
    protected $table = 'damage_details_tab';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'dgn_id',
        'sku',
        'productName',
        'size',
        'uom',
        'batchNo',
        'totalAmount',
        'expirationDate',
        'qtyDamaged',
        'unitCost',
        'comment',
        'created_at',
        'updated_at',
    ];

      public function dgn()
    {
        return $this->belongsTo(DGN::class, 'dgn_id');
    }
}
