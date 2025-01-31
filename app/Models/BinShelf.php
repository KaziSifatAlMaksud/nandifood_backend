<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinShelf extends Model
{
    // Define the table associated with the model
    protected $table = 'bin_shelf';
    protected $fillable = ['shelf_name', 'shelf_no']; // Adjust the fields you want to mass-assign
    protected $primaryKey = 'id';
    public $timestamps = false; // Set to true if you have timestamps in the table
}
