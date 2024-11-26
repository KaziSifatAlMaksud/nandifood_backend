<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uom extends Model
{
    // Specify the table name if it doesn't match the default naming convention
    protected $table = 'uom';
    protected $primaryKey = 'uom_id';
    protected $keyType = 'string';
    protected $fillable = [
        'uom_id',
        'uom_type_id',
        'description',
        'weight',
        'bulk_code',
        'unit',
        'invertory_uom',
        'production_uom',
        'purchase_uom',
        'sales_uom',
        'uom_length',
        'uom_width',
        'uom_height',
        'status',
        'created_at',
        'updated_at',
    ];
    public function uomType()
    {
        return $this->belongsTo(Uom_type::class, 'uom_type_id', 'id'); // Assumes uom_type_id is the foreign key
    }


public static function fullName($uom_id)
{
    // Initialize variables
    $volumem3 = 0;
    $volumeft3 = 0;
    $length_in = 0;
    $width_in = 0;
    $height_in = 0;
    $length_cm = 0;
    $width_cm = 0;
    $height_cm = 0;

    // Find the UOM record
    $uom = Uom::where('uom_id', $uom_id)->first();
    if (!$uom) {
        return null; // Return null if UOM not found
    }

    // Calculate dimensions and volumes based on the unit system
    if ($uom->unit == 0) { 
        // Metric system (cm)
        $length_cm = $uom->uom_length; // Length in cm
        $width_cm = $uom->uom_width;   // Width in cm
        $height_cm = $uom->uom_height; // Height in cm

        // Calculate volume in cubic meters and cubic feet
        $volumem3 = ($length_cm * $width_cm * $height_cm) / 1000000; // cm³ to m³
        $volumeft3 = $volumem3 * 35.3147; // m³ to ft³

        // Convert dimensions to inches
        $length_in = $length_cm * 0.393701; // cm to inches
        $width_in = $width_cm * 0.393701;   // cm to inches
        $height_in = $height_cm * 0.393701; // cm to inches
    } else { 
        // Imperial system (inches)
        $length_in = $uom->uom_length;  // Length in inches
        $width_in = $uom->uom_width;    // Width in inches
        $height_in = $uom->uom_height;  // Height in inches

        // Convert inches to centimeters
        $length_cm = $length_in * 2.54; // inches to cm
        $width_cm = $width_in * 2.54;   // inches to cm
        $height_cm = $height_in * 2.54; // inches to cm

        // Calculate volume in cubic feet and cubic meters
        $volumeft3 = ($length_in * $width_in * $height_in) / 1728; // in³ to ft³
        $volumem3 = $volumeft3 * 0.0283168; // ft³ to m³
    }

    // Find the UOM type
    $uom_type = Uom_type::find($uom->uom_type_id);
    if (!$uom_type) {
        return null; // Return null if UOM type not found
    }

    // Generate names
    $short_name = $uom->uom_id . '(' . $uom_type->uom_name . ')';
    $full_name = $uom->uom_id . ' ' . $uom_type->uom_name . ' (' . $uom->description . ')';

    // Return the result as an array
    return [
        'short_name' => $short_name,
        'full_name' => $full_name,
        'volumem3' => $volumem3,
        'volumeft3' => $volumeft3,
        'length_in' => $length_in,
        'width_in' => $width_in,
        'height_in' => $height_in,
        'length_cm' => $length_cm,
        'width_cm' => $width_cm,
        'height_cm' => $height_cm
    ];
}



    


}
