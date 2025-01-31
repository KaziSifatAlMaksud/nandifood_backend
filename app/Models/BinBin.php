<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinBin extends Model
{
    // Define the table associated with the model (if needed, Laravel will assume it's "bin_bins" by default)
    protected $table = 'bin_bin';
    protected $fillable = ['bin_name', 'bin_no'];
    protected $primaryKey = 'id';
    public $timestamps = false; 
}
