<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BinStatus extends Model
{
    use HasFactory;

    protected $table = 'bin_status';

    protected $primaryKey = 'id'; 
     protected $fillable = [
        'explanation',
        'status',
    ];
   public $timestamps = true;
}
