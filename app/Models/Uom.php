<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uom extends Model
{
    // Specify the table name if it doesn't match the default naming convention
    protected $table = 'uom';

    protected $fillable = [
        'uom_id',
        'uom_type_id',
        'description',
        'weight',
        'bulk_code',
        'unit',
        'invertory_uom',
        'production_uom',
        'purchase_uom',
        'sales_uom',
        'uom_length',
        'uom_width',
        'uom_height',
        'status',
        'created_at',
        'updated_at',
    ];
    public function uom()
    {
        return $this->hasOne(Uom_type::class, 'uom_type_id', 'id');
    }
    


}
