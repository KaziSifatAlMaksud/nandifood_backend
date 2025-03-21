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
        'id',
        'first_name',
        'country',
        'position_id',
        'warehouse_id',
        'middle_name',
        'last_name',
        'email',
        'off_phone',
        'phone',
        'status',
        'address1',
        'address2',
        'city',
        'state',
        'zip_code',
        'certificates1',
        'certificates2',
        'certificates3',
        'certificates4',
        'eff_date',
        'end_date',
        'start_date',
        'last_update',
        'update_by',
        'img1',
        'img2',
        'img3',
        'is_approved',
        'notes',

    ];
    public $timestamps = false; 


    public function employee()
    {
        return $this->belongsTo(Warehouse::class);
    }

    
    public function notes()
    {
        return $this->hasMany(EmployeeNotes::class);
    }

    public function position()
    {
        return $this->belongsTo(Positions::class, 'position_id');
    }
    
    

}
