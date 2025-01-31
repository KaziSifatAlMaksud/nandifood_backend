<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinAisle extends Model
{
    // Define the table associated with the model
    protected $table = 'bin_aisle';

    // Specify which attributes can be mass assigned
    protected $fillable = ['aisle_name', 'aisle_no']; // Adjust the fields you want to mass-assign

    // If the primary key is not 'id', specify it (default is 'id')
    protected $primaryKey = 'id';

    // If you don't want the model to use timestamps (created_at, updated_at)
    public $timestamps = false; // Set to true if you have timestamps in the table
}
