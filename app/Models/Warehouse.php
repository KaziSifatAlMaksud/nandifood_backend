<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;

    // Define the table name if it's different from the plural of the model name
    protected $table = 'warehouse';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id','warehouse_name','global_default_warehouse', 'country_default_warehouse', 'country', 'city', 'zip_code', 'address1', 'address2', 'city', 'email','phone', 'warehouse_contact', 'warehouse_capacity_in_lb', 'warehouse_capacity_in_kg', 'status', 'created_by' ,'created_at', 'updated_at', 'updated_by'
    ];

    // protected $dates = ['created_at', 'updated_at'];

    // public function binLocations()
    // {
    //     return $this->hasMany(BinLocation::class); 
    // }

       public function binLocations()
    {
        return $this->hasMany(BinLocation::class);
    }

      public function getWarehouseFullNameAttribute()
    {
        // Assuming you have 'name' and 'location' attributes in your warehouse table
        return $this->id . ' ' . $this->warehouse_name;
    }
}
