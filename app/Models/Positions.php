<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Positions extends Model
{
     protected $table = 'positions';

    protected $fillable = ['id', 'position_name', 'status'];

  
    public function employee()
    {
        return $this->hasMany(Employee::class);
    }
}
