<?php

namespace App\Http\Controllers;

use App\Models\BinLocation;
use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

use App\Models\Warehouse;

class BinLocationController extends Controller
{
    public function index($id = null)
    {
        // Start the query on BinLocation
        $query = BinLocation::join('warehouse', 'bin_location.warehouse_id', '=', 'warehouse.id')
            ->select([
                'bin_location.id',
                'bin_location.warehouse_id',
                // Assuming you want to use warehouse name and location for full name
                DB::raw("CONCAT(warehouse.warehouse_name, ' - ', warehouse.city, ', ', warehouse.state) AS warehouse_full_name"),
                DB::raw("CONCAT('Z', bin_location.zone_number) AS section_number"),
                DB::raw("CONCAT('Z', bin_location.zone_number, bin_location.section_number) AS full_section_number"),
                DB::raw("CONCAT('Z', bin_location.zone_number, bin_location.section_number, bin_location.aisle_number) AS aisle_number"),
                DB::raw("CONCAT('Z', bin_location.zone_number, bin_location.section_number, bin_location.aisle_number, bin_location.rack_number) AS rack_number"),
                DB::raw("CONCAT('Z', bin_location.zone_number, bin_location.section_number, bin_location.aisle_number, bin_location.rack_number, bin_location.shelf_number) AS shelf_number"),
                'bin_location.bin_number',
                DB::raw("CONCAT('Z', bin_location.zone_number, bin_location.section_number, bin_location.aisle_number, bin_location.rack_number, bin_location.shelf_number, bin_location.bin_number) AS full_bin_location"),
                'bin_location.bin_length',
                'bin_location.bin_width',
                'bin_location.bin_height',
                'bin_location.status',
                'bin_location.description',
                'bin_location.file',
                'bin_location.bin_barcode_img'
            ]);

        // If an id is provided, filter by it
        if ($id) {
            $binlocations = $query->where('bin_location.warehouse_id', $id)->first(); // Fetch a single bin location
            if (!$binlocations) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Bin location not found',
                ], 404);
            }
        } else {
            $binlocations = $query->get(); // Fetch all bin locations
        }

        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => [
                'bin_location' => $binlocations
            ]
        ]);
    }





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



    public function form(Request $request)
{
    // Attempt to validate the request data
    try {
        // Validate the request data
        $validated = $request->validate([
            'continent' => 'required|string|max:255',
            'continental_region' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'country_calling_code' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);

        // Begin database transaction
        DB::beginTransaction();
        $country = Country::create($validated);
        DB::commit();

        // Return a success response
        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => $country
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Return a custom response with validation errors
        return response()->json([
            'status' => 422,
            'errors' => $e->errors() 
        ], 422);

    } catch (\Exception $e) {
        // Rollback the transaction in case of a general exception
        DB::rollBack();

        // Return a response with the exception message
        return response()->json([
            'status' => 500,
            'error' => $e->getMessage()
        ], 500);
    }
}





}
