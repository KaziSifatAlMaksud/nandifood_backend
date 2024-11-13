<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\BinLocation;

class WarehouseController extends Controller
{
    // Fetch all warehouse records
 public function index()
{
    $warehouses = Warehouse::all();
    $binLocations = BinLocation::all();
    return response()->json([
    'message' => 'Retrieved successfully',
    'warehouses' => $warehouses,
    'binLocations' => $binLocations
], 200);

}


    // Fetch a single warehouse by ID (optional)
    public function show($id)
    {
        // Find warehouse by ID or return 404 if not found
        $warehouse = Warehouse::find($id);
        
        // If warehouse is found, return it, else return error response
        if ($warehouse) {
            return response()->json($warehouse);
        } else {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }
    }
}
