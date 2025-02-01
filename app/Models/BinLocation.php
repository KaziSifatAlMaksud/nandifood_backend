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
    protected $primaryKey = 'id'; 
    public $timestamps = true;  // Set to false if your table does not have timestamps
    protected $fillable = [
        'warehouse_id',
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
        'bin_barcode_img',
        'bin_image',
        'bin_weight_kg',
        'eff_date',
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
            ->reduce(function ($totals, $bin) {
                $volume_m3 = 0;
                $capacity_kg = 0;
                $capacity_lb = 0;

                // Ensure bin dimensions are numeric to prevent errors
                if (is_numeric($bin->bin_length) && is_numeric($bin->bin_width) && is_numeric($bin->bin_height)) {
                    if ($bin->metric_unit == '0') { // Metric: centimeters
                        $volume_m3 = ($bin->bin_length * $bin->bin_width * $bin->bin_height) / 1_000_000;
                        $capacity_kg = $bin->bin_weight_kg;
                        $capacity_lb = $capacity_kg * 2.20462;
                    } elseif ($bin->metric_unit == '1') { // Imperial: inches
                        $bin_length_cm = $bin->bin_length * 2.54;
                        $bin_width_cm = $bin->bin_width * 2.54;
                        $bin_height_cm = $bin->bin_height * 2.54;
                        $volume_m3 = ($bin_length_cm * $bin_width_cm * $bin_height_cm) / 1_000_000;
                        $capacity_lb = $bin->bin_weight_kg;
                        $capacity_kg = $capacity_lb / 2.20462;
                    }
                }

                return [
                    'total_volume' => $totals['total_volume'] + $volume_m3,
                    'total_storage_capacity_slp' => $totals['total_storage_capacity_slp'] + ($bin->storage_capacity_slp ?? 0),
                    'total_capacity_kg' => $totals['total_capacity_kg'] + $capacity_kg,
                    'total_capacity_lb' => $totals['total_capacity_lb'] + $capacity_lb,
                ];
            }, [
                'total_volume' => 0,
                'total_storage_capacity_slp' => 0,
                'total_capacity_kg' => 0,
                'total_capacity_lb' => 0
            ]);
    }


    public static function calculateTotalVolume($id)
    {
        // Use `find` for a single bin, as `id` is unique
        $bin = self::find($id);

        if (!$bin) {
            // Handle case where no bin is found
            return 0;
        }

        // Ensure bin dimensions are valid numbers
        $length = $bin->bin_length ?? 0;
        $width = $bin->bin_width ?? 0;
        $height = $bin->bin_height ?? 0;



        return ($length * $width * $height) / 1000000;
    }

    public static function getExtraInfo($id)
{
    $bin = self::find($id);

    if (!$bin) {
        return [
            'bin_length' => 0,
            'bin_width' => 0,
            'bin_height' => 0,
            'volume_m3' => 0,
            'full_bin_location' => null,
            'bin_weight_kg' => 0,
        ];
    }

    $length = $bin->bin_length ?? 0;
    $width = $bin->bin_width ?? 0;
    $height = $bin->bin_height ?? 0;
    $metricUnit = $bin->metric_unit ?? 0;
    $bin_weight_kg = $bin->bin_weight_kg ?? 0;

    // Concatenate full bin location (including weight if necessary)
    $fullBinLocation = implode('', [
        $bin->zone_number,
        $bin->section_number,
        $bin->aisle_number,
        $bin->rack_number,
        $bin->shelf_number,
        $bin->bin_number,
    ]);

    // If the metric_unit is 1 (imperial), convert dimensions to cm (Inches to cm)
    if ($metricUnit == 1) {
        $length *= 2.54; // Inches to cm
        $width *= 2.54;  // Inches to cm
        $height *= 2.54; // Inches to cm

        // Convert bin weight from pounds (lb) to kilograms (kg) if it's in pounds
        $bin_weight_kg *= 0.453592;  // lb to kg
    }

    // Calculate volume in cubic centimeters (cm³) and convert to cubic meters (m³)
    $volumeInCm3 = $length * $width * $height;
    $volumeInM3 = $volumeInCm3 / 1_000_000; // 1 m³ = 1,000,000 cm³

    return [
        'bin_length' => $length,
        'bin_width' => $width,
        'bin_height' => $height,
        'volume_m3' => $volumeInM3,
        'full_bin_location' => $fullBinLocation,
        'bin_weight_kg' => $bin_weight_kg,
    ];
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
