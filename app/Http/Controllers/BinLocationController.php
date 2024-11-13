<?php

namespace App\Http\Controllers;

use App\Models\BinLocation;
use Illuminate\Http\Request;

class BinLocationController extends Controller
{
    public function store(Request $request)
    {
        // Dummy data (use this for testing purposes)
        $binLocation = BinLocation::create([
            'warehouse_id' => 'W001',
            'effective_date' => '2024-11-13',
            'storage_type_id' => 'ST001',
            'asset_type_id' => 'AT001',
            'zone_number' => 'Z01',
            'zone_name' => 'Zone 1',
            'section_number' => 1,
            'section_name' => 'Section 1',
            'aisle_number' => 1,
            'aisle_name' => 'Aisle 1',
            'rack_number' => 1,
            'rack_name' => 'Rack 1',
            'shelf_number' => 1,
            'shelf_name' => 'Shelf 1',
            'bin_number' => 1,
            'bin_name' => 'Bin 1',
            'metric_unit' => 'cm',
            'bin_length' => 50.00,
            'bin_width' => 30.00,
            'bin_height' => 20.00,
            'storage_capacity_slp' => 300.00,
            'start_date' => now(),
            'end_date' => now()->addYears(1),
            'status' => 'active',
            'created_by' => 'user1',
            'updated_by' => 'user1',
            'description' => 'Sample bin location',
            'file' => 'path/to/file.pdf',
            'bin_barcode_img' => 'path/to/barcode_image.jpg', // Assuming static path for the dummy image
        ]);

        return response()->json([
            'message' => 'Dummy Bin Location created successfully!',
            'bin_location' => $binLocation
        ], 201); // HTTP 201 Created
    }
}
