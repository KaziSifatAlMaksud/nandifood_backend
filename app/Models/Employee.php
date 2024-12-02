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
        'first_name',
        'country_id',
        'position_id',
        'warehouse_id',
        'middle_name',
        'last_name',
        'email',
        'off_phone',
        'phone',
        'status'
    ];
    // public $timestamps = false; 


    public function employee()
    {
        return $this->belongsTo(Warehouse::class);
    }
    
    

}
