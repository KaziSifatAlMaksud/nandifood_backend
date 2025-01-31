<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinRack extends Model
{
    // Define the table associated with the model
    protected $table = 'bin_rack';
    protected $fillable = ['rack_name', 'rack_no']; // Adjust the fields you want to mass-assign
    protected $primaryKey = 'id';
    public $timestamps = false; // Set to true if you have timestamps in the table
}
