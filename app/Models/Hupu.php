<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Hupu extends Model
{
       use HasFactory;
     protected $table = 'hupu';
    // protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'hu_pu_id',
        'hu_pu_code',
        'hu_pu_type',
        'flex',
        'pu_hu_name',
        'description',                
        'unit',
        'length',
        'width',
        'height',
        'hu_empty_weight',
        'min_weight',
        'hu_loaded_weight',
        'max_weight',
        'status',
        'bulk_code',
        'eff_date'
    ];

    public function uomType()
    {
        return $this->belongsTo(Uom_type::class,'id', 'pu_hu_name'); // Assumes uom_type_id is the foreign key
    }


    public function linkedhupus()
    {
        return $this->hasMany(Uom_linked::class,  'uom_id'); // Assumes uom_id is the foreign key
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

    $min_weight_kg = 0;
    $max_weight_kg = 0;
    $min_weight_lb = 0;
    $max_weight_lb = 0;

    // Find the UOM record
    $hu_list = Hupu::where('id', $id)->first();
    if (!$hu_list) {
        return null; // Return null if UOM not found
    }

    // Calculate dimensions and volumes based on the unit system
    if ($hu_list->unit == 0) {
        // Metric system (cm)
        $length_cm = $hu_list->length; // Length in cm
        $width_cm = $hu_list->width;   // Width in cm (assuming 'weight' is for width)
        $height_cm = $hu_list->height; // Height in cm
        $min_weight_kg = $hu_list->min_weight; // Min weight in kg
        $max_weight_kg = $hu_list->max_weight; // Max weight in kg
        $volumem3 = ($length_cm * $width_cm * $height_cm) / 1000000; // cm³ to m³
        $volumeft3 = $volumem3 * 35.3147; // m³ to ft³

        // Convert dimensions to inches
        $length_in = $length_cm * 0.393701; // cm to inches
        $width_in = $width_cm * 0.393701;   // cm to inches
        $height_in = $height_cm * 0.393701; // cm to inches

        $min_weight_lb = $min_weight_kg * 2.20462;  // kg to lb
        $max_weight_lb = $max_weight_kg * 2.20462;  // kg to lb
    } else { 
        // Imperial system (inches)
        $length_in = $hu_list->length;  // Length in inches
        $width_in = $hu_list->width;   // Width in inches (assuming 'weight' is for width)
        $height_in = $hu_list->height;  // Height in inches
        $min_weight_lb = $hu_list->min_weight; // Min weight in lb
        $max_weight_lb = $hu_list->max_weight; // Max weight in lb
        // Convert inches to centimeters
        $length_cm = $length_in * 2.54; // inches to cm
        $width_cm = $width_in * 2.54;   // inches to cm
        $height_cm = $height_in * 2.54; // inches to cm
        $min_weight_kg = $min_weight_lb * 0.453592; // lb to kg
        $max_weight_kg = $max_weight_lb * 0.453592; // lb to kg

        // Calculate volume in cubic feet and cubic meters
        $volumeft3 = ($length_in * $width_in * $height_in) / 1728; // in³ to ft³
        $volumem3 = $volumeft3 * 0.0283168; // ft³ to m³
    }

    // Find the UOM type
    $uom_type = Uom_type::find($hu_list->hu_pu_type);
    if (!$uom_type) {
        return null; // Return null if UOM type not found
    }

    // Generate names
    $short_name = $hu_list->hu_pu_id . '(' . $uom_type->uom_name . ')';
    $full_name = $hu_list->hu_pu_id . ' ' . $uom_type->uom_name . ' (' . $hu_list->description . ')';

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
        'height_cm' => $height_cm,
        'min_weight_kg' => $min_weight_kg,
        'max_weight_kg' => $max_weight_kg,
        'min_weight_lb' => $min_weight_lb,
        'max_weight_lb' => $max_weight_lb
        
    ];
}


}
