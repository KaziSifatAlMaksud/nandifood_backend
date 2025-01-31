<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinZones extends Model
{
    // Specify the table name if it's different from the plural form of the model
    protected $table = 'bin_zones';
    protected $fillable = ['id', 'zone_name', 'zone_no'];
    public $timestamps = false; 
}
