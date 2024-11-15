<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    // Define the table name (if it is not the default plural form of the model name)
    protected $table = 'country';

    // Define the primary key column if it's not 'id' (optional)
    protected $primaryKey = 'id'; // Optional if using the default 'id'
    protected $fillable = [
        'continent',
        'continental_region',
        'country',
        'country_calling_code',
        'state',
        'city',
    ];
   public $timestamps = false; 
}
