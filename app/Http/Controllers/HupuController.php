<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hupu;

class HupuController extends Controller
{
   public function hu_list()
    {
        $hu_lists = Hupu::select([
            'id',
            'hu_pu_code',
            'pu_hu_name',
            'description',
            'unit',
            'length',
            'weight',
            'height'
        ])
        ->where('hu_pu_type', 1)
        ->get();

        $hu_lists = $hu_lists->map(function ($hu_list) {
            // Variables for centimeters and inches
            $length_cm = $width_cm = $height_cm = null;
            $length_in = $width_in = $height_in = null;

            // Convert based on unit type
            if ($hu_list->unit == 0) {  // Assuming 0 is for centimeters
                // Values in cm
                $length_cm = $hu_list->length;
                $width_cm = $hu_list->weight;
                $height_cm = $hu_list->height;
            } else {  // Assuming 1 is for inches
                // Values in inches
                $length_in = $hu_list->length;
                $width_in = $hu_list->weight;
                $height_in = $hu_list->height;
            }
            // dd($hu_list->id);
            // Get full name and volume calculations
            $result = Hupu::fullName($hu_list->id);

            $hu_list->short_name = $result['short_name'];
            $hu_list->full_name = $result['full_name'];
            $hu_list->volumem3 = $result['volumem3'];
            $hu_list->volumeft3 = $result['volumeft3'];

            // Add values for both inches and centimeters based on unit
            $hu_list->length_in = $length_in ?? $result['length_in'];  // Default to result if not set
            $hu_list->width_in = $width_in ?? $result['width_in'];
            $hu_list->height_in = $height_in ?? $result['height_in'];
            $hu_list->length_cm = $length_cm ?? $result['length_cm'];
            $hu_list->width_cm = $width_cm ?? $result['width_cm'];
            $hu_list->height_cm = $height_cm ?? $result['height_cm'];

            return $hu_list;
        });

        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => $hu_lists
        ]);
    }


     public function pu_list()
    {
        $pu_list = Hupu::where("hu_pu_type", 2)->get();        
        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => $pu_list
        ]);

    }
}
