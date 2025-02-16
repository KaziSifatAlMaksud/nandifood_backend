<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;

    // Define the table name if it's different from the plural of the model name
    protected $table = 'warehouse';
    // public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id','warehouse_name','global_default_warehouse', 'country_default_warehouse', 'country', 'state','city', 'zip_code', 'address1', 'address2', 'city', 'email','phone', 'warehouse_contact', 'warehouse_capacity_in_lb', 'warehouse_capacity_in_kg', 'status', 'created_by' ,'created_at', 'updated_at', 'updated_by','emergency_phone', 'warehouse_manager','warehouse_supervisor','eff_date','loc_work_week','work_week_days','bus_hours_open','bus_hours_close','entity','wh_image', 'warehouse_notes','warehouse_safety','warehouse_compliance','is_approved'
    ];

    public function binLocations()
    {
        return $this->hasMany(BinLocation::class);
    }
    public function employee()
    {
        return $this->hasMany(Employee::class);
    }

           public function warehouse_attachment()
    {
        return $this->hasMany(WarehouseAttachment::class);
    }
      public function getWarehouseFullNameAttribute()
    {
        return $this->id . ' ' . $this->warehouse_name;
    }
}

