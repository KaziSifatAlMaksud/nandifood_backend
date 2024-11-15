<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;
    protected $table = 'employee';
    protected $primaryKey = 'id';
    protected $keyType = 'string'; 
    protected $fillable = [
        'employee_name',
        'country_id',
        'position_id',
        'default_warehouse',
    ];

    // Optionally, disable timestamps if you don't want 'created_at' and 'updated_at' columns
    public $timestamps = true; 


    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'default_warehouse'); // Make sure the foreign key is correct
    }

}
