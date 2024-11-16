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
            'result' => $binlocations 
            
        ]);
    }


     public function destroy($id)
    {
        $country = Country::find($id);

        // Check if the country exists
        if (!$country) {
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => 'Country not found.',
            ], 404);
        }

        // Delete the country
        $country->delete();

        // Return a success response
        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'Country deleted successfully.',
        ], 200);
    }





    public function store(Request $request, $war_id)
    {

        try {
        // Validate the request data
        $validated = $request->validate([
            'warehouse_id' => 'required|string',
            'effective_date' => 'required| date',
            'storage_type_id' => 'required|string',
            'asset_type_id' => 'required|string',
            'zone_number' => 'required|string',
            'zone_name' => 'required|string'
        ]);

        // Begin database transaction
        DB::beginTransaction();
        $binlocation = BinLocation::create($validated);
        DB::commit();
        return response()->json([
            'status' => 200,
            'message' => 'Ok',
            'result' => $binlocation
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
