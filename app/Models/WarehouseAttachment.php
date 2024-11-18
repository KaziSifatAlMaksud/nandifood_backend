<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseAttachment extends Model
{
    protected $table = 'warehouse_attachment';
    protected $fillable = [
        'id',
        'type',
        'warehouse_id',
        'file',
        'created_by',
        'updated_by',
        'date_uploaded',
        'description',
        'status',
        'created_at',
        'updated_at',
    ];
}
