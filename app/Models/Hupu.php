<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hupu extends Model
{
     protected $table = 'hupu';
    // protected $primaryKey = 'uom_id';

    protected $fillable = [
        'hu_pu_code',
        'hu_pu_type',
        'flex',
        'pu_hu_name',
        'description',                
        'unit',
        'length',
        'weight',
        'height',
        'hu_empty_weight',
        'hu_minimum_weight',
        'hu_loaded_weight',
        'hu_maximum_weight'
    ];

    public function uomType()
    {
        return $this->belongsTo(Uom_type::class,'id', 'pu_hu_name'); // Assumes uom_type_id is the foreign key
    }

public static function fullName($id)
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
    $hu_list = Hupu::where('id', $id)->first();
    if (!$hu_list) {
        return null; // Return null if UOM not found
    }

    // Calculate dimensions and volumes based on the unit system
    if ($hu_list->unit == 0) {
        // Metric system (cm)
        $length_cm = $hu_list->length; // Length in cm
        $width_cm = $hu_list->weight;   // Width in cm (assuming 'weight' is for width)
        $height_cm = $hu_list->height; // Height in cm

        // Calculate volume in cubic meters and cubic feet
        $volumem3 = ($length_cm * $width_cm * $height_cm) / 1000000; // cm³ to m³
        $volumeft3 = $volumem3 * 35.3147; // m³ to ft³

        // Convert dimensions to inches
        $length_in = $length_cm * 0.393701; // cm to inches
        $width_in = $width_cm * 0.393701;   // cm to inches
        $height_in = $height_cm * 0.393701; // cm to inches
    } else { 
        // Imperial system (inches)
        $length_in = $hu_list->length;  // Length in inches
        $width_in = $hu_list->weight;   // Width in inches (assuming 'weight' is for width)
        $height_in = $hu_list->height;  // Height in inches

        // Convert inches to centimeters
        $length_cm = $length_in * 2.54; // inches to cm
        $width_cm = $width_in * 2.54;   // inches to cm
        $height_cm = $height_in * 2.54; // inches to cm

        // Calculate volume in cubic feet and cubic meters
        $volumeft3 = ($length_in * $width_in * $height_in) / 1728; // in³ to ft³
        $volumem3 = $volumeft3 * 0.0283168; // ft³ to m³
    }

    // Find the UOM type
    $uom_type = Uom_type::find($hu_list->pu_hu_name);
    if (!$uom_type) {
        return null; // Return null if UOM type not found
    }

    // Generate names
    $short_name = $hu_list->hu_pu_code . '(' . $uom_type->uom_name . ')';
    $full_name = $hu_list->hu_pu_code . ' ' . $uom_type->uom_name . ' (' . $hu_list->description . ')';

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
