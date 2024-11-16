<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class BinLocation extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'bin_location';  // Optional, only needed if your table name is not the plural form of the model


    // The primary key for the model (optional if using default 'id')
    protected $primaryKey = 'id'; 

    // Disable the timestamps if your table doesn't have 'created_at' and 'updated_at' columns
    public $timestamps = true;  // Set to false if your table does not have timestamps

    // Specify which columns are mass assignable
    protected $fillable = [
        'warehouse_id',
        'effective_date',
        'storage_type_id',
        'asset_type_id',
        'zone_number',
        'zone_name',
        'section_number',
        'section_name',
        'aisle_number',
        'aisle_name',
        'rack_number',
        'rack_name',
        'shelf_number',
        'shelf_name',
        'bin_number',
        'bin_name',
        'metric_unit',
        'bin_length',
        'bin_width',
        'bin_height',
        'storage_capacity_slp',
        'start_date',
        'end_date',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'description',
        'file',
        'bin_barcode_img'
    ];

    // Optionally, define the data types of attributes (for casting)
    protected $casts = [
        'effective_date' => 'date',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'bin_length' => 'decimal:2',
        'bin_width' => 'decimal:2',
        'bin_height' => 'decimal:2',
        'storage_capacity_slp' => 'decimal:2',
        // Add any other date or decimal casts as needed
    ];



     public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id'); // Make sure the foreign key is correct
    }


   
      /**
     * Calculate total volume for all bins in a warehouse.
     *
     * @param int $warehouseId
     * @return float
     */

    public static function calculateTotalVolumeForWarehouse($warehouseId)
    {
        return self::where('warehouse_id', $warehouseId)
            ->get()
            ->reduce(function ($total, $bin) {
                $volume = ( $bin->bin_length * $bin->bin_width * $bin->bin_height);
                return $total + $volume;
            }, 0);
    }
 
    //   public static function calculateTotalVolumeForWarehouse($warehouseId)
    // {
    //     return self::where('warehouse_id', $warehouseId)
    //         ->get()
    //         ->reduce(function ($total, $bin) {
    //             $volume = ( $bin->bin_length * $bin->bin_width * $bin->bin_height);
    //             return $total + $volume;
    //         }, 0);
    // }


}