<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uom_type extends Model
{
     protected $table = 'uom_type';

    protected $fillable = ['id', 'uom_name', 'level'];


        public function uom()
        {
              return $this->hasMany(Uom::class, 'uom_type_id', 'id');
        }
        public function hupu()
        {
              return $this->hasMany(Hupu::class, 'id', 'pu_hu_name');
        }

}
