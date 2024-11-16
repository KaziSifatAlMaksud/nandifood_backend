<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uom_type extends Model
{
     protected $table = 'uom';

    protected $fillable = ['id', 'uom_name', 'level']


   public function uoms()
    {
        return $this->hasMany(Uom::class, 'uom_type_id', 'id');
    }
}
