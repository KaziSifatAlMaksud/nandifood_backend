<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BinStorageType extends Model
{
    use HasFactory;

    // Define the table name (if it's not the default plural of the model name)
    protected $table = 'bin_storage_type';

    // Define the primary key column if it's not 'id'
    protected $primaryKey = 'id'; // Optional if using the default 'id'

    // Define the fields that are mass assignable
    protected $fillable = [
        'storage_type',
    ];

    // Optionally, define the timestamps if you're not using the default 'created_at' and 'updated_at' columns
    public $timestamps = true; 
}
