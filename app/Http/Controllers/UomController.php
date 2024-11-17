<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Uom; 
use App\Models\Hupu;
use Illuminate\Support\Facades\DB;

class UomController extends Controller
{
    public function index()
    {
    $uoms = Uom::select([
        'uom_id',
        'description',
        'bulk_code',
        'unit',
        'inventory_uom',
        'production_uom',
        'purchase_uom',
        'sales_uom'
    ])->get();

    $uoms = $uoms->map(function ($uom) {
        if ($uom->unit == 0) {      
            $length_cm = $uom->uom_length;  
            $width_cm = $uom->uom_width;  
            $height_cm = $uom->uom_height; 
        } else { 
            $length_in = $uom->uom_length;
            $width_in = $uom->uom_width; 
            $height_in = $uom->uom_height;
        }
        $result = Uom::fullName($uom->uom_id);
        $uom->short_name = $result['short_name']; 
        $uom->full_name = $result['full_name']; 
        $uom->volumem3 = $result['volumem3']; 
        $uom->volumeft3 = $result['volumeft3'];
        $uom->length_in = $result['length_in'];
        $uom->width_in = $result['width_in'];
        $uom->height_in = $result['height_in'];
        $uom->length_cm = $result['length_cm']; 
        $uom->width_cm = $result['width_cm'];   
        $uom->height_cm = $result['height_cm'];

        return $uom;
    });



        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => $uoms
        ]);
    }

  
}
