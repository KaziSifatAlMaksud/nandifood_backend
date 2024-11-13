<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
       use HasFactory;

    // Define the table name (if it's not the default plural of the model name)
    protected $table = 'employees';

    // Define the primary key column if it's not 'id' (optional)
    protected $primaryKey = 'id'; // Optional if using the default 'id'

    // Define the fields that are mass assignable
    protected $fillable = [
        'employee_name',
        'country_id',
        'position_id',
        'default_warehouse',
    ];

    // Optionally, disable timestamps if you don't want 'created_at' and 'updated_at' columns
    public $timestamps = true; 
}
