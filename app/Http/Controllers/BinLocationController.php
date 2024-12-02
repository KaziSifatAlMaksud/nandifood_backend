<?php

namespace App\Http\Controllers;

use App\Models\BinLocation;
use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BinLocationController;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;



use App\Models\Warehouse;

class BinLocationController extends Controller
{
public function index(Request $request)
{
    // Start the query on BinLocation
    $query = BinLocation::join('warehouse', 'bin_location.warehouse_id', '=', 'warehouse.id')
        ->select([
            'bin_location.id',
            'bin_location.warehouse_id',
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

    // Check if an ID is provided
    $id = $request->input('id');
    $limit = (int) $request->input('limit', 5);

    if ($id) {
        // Filter by ID and retrieve the data
        $binLocations = $query->where('bin_location.warehouse_id', $id)->paginate($limit);

        // Transform the collection
        $binLocations->getCollection()->transform(function ($binLocation) {
            $binLocation->file_url = $binLocation->file ? Storage::url($binLocation->file) : null;
            $binLocation->bin_barcode_img_url = $binLocation->bin_barcode_img ? Storage::url($binLocation->bin_barcode_img) : null;
            return $binLocation;
        });

        // Return the filtered result
        return response()->json([
            'status' => 200,
            'message' => 'Filtered by warehouse ID',
            'result' => $binLocations
        ]);
    }

    // Implement search functionality if no ID is provided
    $search = $request->input('search');
    if ($search) {
        $terms = explode(' ', $search); // Split the search string into terms
        $query->where(function ($subQuery) use ($terms) {
            foreach ($terms as $term) {
                $subQuery->orWhere('warehouse.warehouse_name', 'LIKE', '%' . $term . '%')
                    ->orWhere('bin_location.description', 'LIKE', '%' . $term . '%');
            }
        });
    }

    // Pagination logic
    $binLocations = $query->paginate($limit);

    // Transform the result to add file and barcode image URLs
    $binLocations->getCollection()->transform(function ($binLocation) {
        $binLocation->file_url = $binLocation->file ? Storage::url($binLocation->file) : null;
        $binLocation->bin_barcode_img_url = $binLocation->bin_barcode_img ? Storage::url($binLocation->bin_barcode_img) : null;
        return $binLocation;
    });

    // Return paginated results if no ID is provided
    return response()->json([
        'status' => 200,
        'message' => 'Ok.',
        'result' => $binLocations
    ]);
}


public function show($id)
{
    // Retrieve the BinLocation by ID
    $binLocation = BinLocation::findOrFail($id);

    // Check if the bin location exists
    if (!$binLocation) {
        return response()->json([
            'status' => 404,
            'success' => false,
            'message' => 'Bin location not found.',
        ], 404);
    }

    // Prepare the file URLs
    $binLocation->bin_image = $binLocation->bin_image ? Storage::url($binLocation->bin_image) : null;
    $binLocation->bin_barcode_img = $binLocation->bin_barcode_img ? Storage::url($binLocation->bin_barcode_img) : null;
    $binLocation->file = $binLocation->file ? Storage::url($binLocation->file) : null;

    // Return the bin location with the updated file URLs
    return response()->json([
        'status' => 200,
        'message' => 'Ok',
        'result' => [
            'data' => $binLocation
           
        ],
    ]);
}

public function edit($id)
{
    // Retrieve the BinLocation by ID
    $binLocation = BinLocation::findOrFail($id);

    // Check if the bin location exists
    if (!$binLocation) {
        return response()->json([
            'status' => 404,
            'success' => false,
            'message' => 'Bin location not found.',
        ], 404);
    }

    // Prepare the file URLs
    $binLocation->bin_image = $binLocation->bin_image ? Storage::url($binLocation->bin_image) : null;
    $binLocation->bin_barcode_img = $binLocation->bin_barcode_img ? Storage::url($binLocation->bin_barcode_img) : null;
    $binLocation->file = $binLocation->file ? Storage::url($binLocation->file) : null;

    // Return the bin location with the updated file URLs
    return response()->json([
        'status' => 200,
        'message' => 'Ok',
        'result' => [
            'data' => $binLocation
           
        ],
    ]);
}



public function update(Request $request, $id)
{
    $binlocation = BinLocation::find($id);
    if (!$binlocation) {
        return response()->json([
            'status' => 404,
            'success' => false,
            'message' => 'Bin location not found.',
        ], 404);
    }
    // Update the BinLocation with the validated data
    $binlocation->update($request->all());

    // Return a successful response
    return response()->json([
        'status' => 200,
        'message' => 'Update Ok.!',
        'result' => [
            'data' => $binlocation,
        ],
    ], 200);
}



    public function store(Request $request)
    {
    try {
        // Validate the request data
        // $validated = $request->validate([
        //     'warehouse_id' => 'required|string|max:8',
        //     'effective_date' => 'required|date',
        //     'storage_type_id' => 'required|string|max:8',
        //     'asset_type_id' => 'required|string|max:8',
        //     'zone_number' => 'required|string|max:8',
        //     'zone_name' => 'required|string|max:255',
        //     'section_number' => 'nullable|integer',
        //     'aisle_number' => 'nullable|integer', // Use integer for numbers
        //     'rack_number' => 'nullable|integer', // Use integer for numbers
        //     'shelf_number' => 'nullable|integer', // Use integer for numbers
        //     'bin_number' => 'nullable|integer', // Use integer for numbers
        //     'metric_unit' => 'required|string|max:255',
        //     'bin_length' => 'required|string|max:255',
        //     'bin_width' => 'required|string|max:255',
        //     'bin_height' => 'required|string|max:255',
        //     'status' => 'nullable|string|max:255',
        //     'description' => 'nullable|string|max:255', // Optional, so make it nullable
        //     'file' => 'nullable|file|mimes:jpg,png,pdf', // Assuming file is an uploaded file
        //     'bin_barcode_img' => 'nullable|file|mimes:jpg,png,jpeg', // Optional, so make it nullable
        // ]);

        // Begin database transaction
        DB::beginTransaction();
        $data = $request->all();
       // Handle file upload for 'file' field (general file)
       if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('uploads/files', 'public');
            $data['file'] = $filePath;
        }

        // Handle file upload for 'bin_image' field (image file)
        if ($request->hasFile('bin_image')) {
            $binImagePath = $request->file('bin_image')->store('uploads/bin_images', 'public');
            $data['bin_image'] = $binImagePath;
        }

        // Handle file upload for 'bin_barcode_img' field (barcode image)
        if ($request->hasFile('bin_barcode_img')) {
            $barcodeImagePath = $request->file('bin_barcode_img')->store('uploads/barcodes', 'public');
            $data['bin_barcode_img'] = $barcodeImagePath;
        }
        $binlocation = BinLocation::create($data);
        // $binlocation = BinLocation::create($validated);
        DB::commit();

        // Return a success response
        return response()->json([
            'status' => 200,
            'message' => 'Bin location created successfully',
            'result' => $binlocation
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => 422,
            'errors' => $e->errors() 
        ], 422);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 500,
            'error' => $e->getMessage()
        ], 500);
    }
}


     public function destroy($id)
    {
         $binLocation = BinLocation::find($id);

        // Check if the country exists
        if (!$binLocation) {
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => 'Bin location not found.',
            ], 404);
        }
            $binLocation->delete();

        // Return a success response
            return response()->json([
                'status' => '200',
                'message' => 'Bin location deleted successfully'
            ], 200);
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
                return response()->json([
                    'status' => 500,
                    'error' => $e->getMessage()
                ], 500);
            }
        }





}
