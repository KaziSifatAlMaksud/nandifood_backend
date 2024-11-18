<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseImage extends Model
{
    protected $table = 'warehouse_image';
    protected $fillable = [
        'id',
        'warehouse_id',
        'image',
        'order',
        'created_at',
        'updated_at',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
