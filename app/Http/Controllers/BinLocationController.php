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
            'bin_display_name',
            // ,
            // DB::raw("CONCAT(warehouse.warehouse_name, ' - ', warehouse.city, ', ', warehouse.state) AS warehouse_full_name"),
            // DB::raw("CONCAT('Z', bin_location.zone_number) AS section_number"),
            // DB::raw("CONCAT('Z', bin_location.zone_number, bin_location.section_number) AS full_section_number"),
            // DB::raw("CONCAT('Z', bin_location.zone_number, bin_location.section_number, bin_location.aisle_number) AS aisle_number"),
            // DB::raw("CONCAT('Z', bin_location.zone_number, bin_location.section_number, bin_location.aisle_number, bin_location.rack_number) AS rack_number"),
            // DB::raw("CONCAT('Z', bin_location.zone_number, bin_location.section_number, bin_location.aisle_number, bin_location.rack_number, bin_location.shelf_number) AS shelf_number"),
            // 'bin_location.bin_number',
            // DB::raw("CONCAT('Z', bin_location.zone_number, bin_location.section_number, bin_location.aisle_number, bin_location.rack_number, bin_location.shelf_number, bin_location.bin_number) AS full_bin_location"),
            'bin_location.bin_length',
            'bin_location.bin_width',
            'bin_location.bin_height',
            'bin_location.status',
            'bin_location.description',
            'bin_location.file',
            'bin_location.bin_barcode_img',
            'bin_location.bin_image'
        ]);
    // Check if an ID is provided
    $id = $request->input('id');
    $limit = (int) $request->input('limit', 5);

    if ($id) {
        // Filter by ID and retrieve the data
        $binLocations = $query->where('bin_location.warehouse_id', $id)->paginate($limit);

        // Transform the collection
        $binLocations->getCollection()->transform(function ($binLocation) {
            $binLocation->file_url = $binLocation->file ? Storage::disk('spaces')->url($binLocation->file) : null;
            $binLocation->bin_barcode_img_url = $binLocation->bin_barcode_img ? Storage::disk('spaces')->url($binLocation->bin_barcode_img) : null;
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
        $binLocation->bin_image = $binLocation->bin_image ? Storage::disk('spaces')->url($binLocation->bin_image) : null;
        $binLocation->file_url = $binLocation->file ? Storage::disk('spaces')->url($binLocation->file) : null;
        $binLocation->bin_barcode_img_url = $binLocation->bin_barcode_img ? Storage::disk('spaces')->url($binLocation->bin_barcode_img) : null;
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
    // Retrieve the BinLocation by ID, joining with the warehouse table
    $binLocation = BinLocation::join('warehouse', 'bin_location.warehouse_id', '=', 'warehouse.id')
        ->select([
            'bin_location.id',
            'bin_location.warehouse_id',
            'bin_location.eff_date',
            'bin_location.storage_type_id',
            'bin_location.asset_type_id',
            'bin_location.zone_number',
            'bin_location.zone_name',
            'bin_location.section_number',
            'bin_location.section_name',
            'bin_location.aisle_number',
            'bin_location.aisle_name',
            'bin_location.rack_number',
            'bin_location.rack_name',
            'bin_location.shelf_number',
            'bin_location.shelf_name',
            'bin_location.bin_number',
            'bin_location.bin_name',
            'bin_location.metric_unit',
            'bin_location.bin_length',
            'bin_location.bin_width',
            'bin_location.bin_height',
            'bin_location.storage_capacity_slp',
            'bin_location.start_date',
            'bin_location.end_date',
            'bin_location.status',
            'bin_location.created_at',
            'bin_location.created_by',
            'bin_location.updated_at',
            'bin_location.updated_by',
            'bin_location.description',
            'bin_location.bin_image',
            'bin_location.file',
            'bin_location.bin_barcode_img',         
            'bin_location.bin_weight_kg',
            'bin_location.bin_display_name',
        

            // Warehouse-related and formatted values
            // DB::raw("CONCAT(warehouse.warehouse_name, ' - ', warehouse.city, ', ', warehouse.state) AS warehouse_full_name"),
            // DB::raw("CONCAT( bin_location.zone_number) AS section_number"),
            // DB::raw("CONCAT(bin_location.zone_number, bin_location.section_number) AS full_section_number"),
            // DB::raw("CONCAT(bin_location.zone_number, bin_location.section_number, bin_location.aisle_number) AS aisle_number"),
            // DB::raw("CONCAT(bin_location.zone_number, bin_location.section_number, bin_location.aisle_number, bin_location.rack_number) AS rack_number"),
            // DB::raw("CONCAT(bin_location.zone_number, bin_location.section_number, bin_location.aisle_number, bin_location.rack_number, bin_location.shelf_number) AS shelf_number"),
            // DB::raw("CONCAT(bin_location.zone_number, bin_location.section_number, bin_location.aisle_number, bin_location.rack_number, bin_location.shelf_number, bin_location.bin_number) AS full_bin_location")
        ])
        ->where('bin_location.id', $id)
        ->orderBy('bin_location.id', 'DESC')
        ->firstOrFail(); // Retrieve the first result or fail if not found
        // Prepare the file URLs
        $binLocation->bin_image = $binLocation->bin_image ? Storage::disk('spaces')->url($binLocation->bin_image) : null;
        $binLocation->bin_barcode_img = $binLocation->bin_barcode_img ? Storage::disk('spaces')->url($binLocation->bin_barcode_img) : null;
        $binLocation->file = $binLocation->file ? Storage::disk('spaces')->url($binLocation->file) : null;


    // Calculate total volume (if needed)
    $totals = BinLocation::calculateTotalVolume($id);
    $binLocation->volume_m3 = $totals;

    // Check if the bin location exists
    if (!$binLocation) {
        return response()->json([
            'status' => 404,
            'success' => false,
            'message' => 'Bin location not found.',
        ], 404);
    }

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
    $binLocation->bin_image = $binLocation->bin_image ? Storage::disk('spaces')->url($binLocation->bin_image) : null;
    $binLocation->bin_barcode_img = $binLocation->bin_barcode_img ? Storage::disk('spaces')->url($binLocation->bin_barcode_img) : null;
    $binLocation->file = $binLocation->file ? Storage::disk('spaces')->url($binLocation->file) : null;

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
    DB::beginTransaction(); // Start the transaction

    try {
        // Find the BinLocation record
        $binlocation = BinLocation::find($id);
        if (!$binlocation) {
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => 'Bin location not found.',
            ], 404);
        }

        // Update all request data except file fields
        $binlocation->fill($request->except(['file', 'bin_image', 'bin_barcode_img']));

        // Handle file uploads
        $fileFields = ['file', 'bin_image', 'bin_barcode_img'];
        foreach ($fileFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $file = $request->file($fileField);

                if ($file->isValid()) {
                    // Delete old file if it exists
                    if (!empty($binlocation->$fileField) && Storage::disk('spaces')->exists($binlocation->$fileField)) {
                        Storage::disk('spaces')->delete($binlocation->$fileField);
                    }

                    // Upload new file
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = "uploads/warehouse_file/{$fileName}";
                    Storage::disk('spaces')->put($filePath, file_get_contents($file), ['visibility' => 'public']);

                    // Update model attribute
                    $binlocation->$fileField = $filePath;
                } else {
                    return response()->json([
                        'status' => 400,
                        'success' => false,
                        'message' => "Invalid file upload for {$fileField}!",
                    ], 400);
                }
            }
        }
        $binlocation->save();

        DB::commit(); // Commit the transaction

        // Return a successful response
        return response()->json([
            'status' => 200,
            'message' => 'Binlocation update successful.',
            'result' => $binlocation,
        ], 200);
    } catch (\Exception $e) {
        DB::rollback(); // Rollback the transaction

        // Return an error response
        return response()->json([
            'status' => 500,
            'success' => false,
            'message' => 'An error occurred during the update process.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function store(Request $request)
{
    try {
        // Begin database transaction
        DB::beginTransaction();

        try {
            $data = $request->all();
            
            // Handle file upload for 'file' field (general file)
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName(); 
                $path = "uploads/warehouse_file/{$fileName}";
                $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);
                if ($uploaded) {
                    $data['file'] = $path;  // Save the correct file path
                }
            }


            if ($request->hasFile('bin_image')) {
                $bin_image = $request->file('bin_image');
                $bin_imageName = time() . '_' . $bin_image->getClientOriginalName(); 
                $bin_path = "uploads/warehouse_file/{$bin_imageName}";
                
                // Upload the image to DigitalOcean Spaces using 'put' method
                $uploaded = Storage::disk('spaces')->put($bin_path, file_get_contents($bin_image), ['visibility' => 'public']);
                
                // If upload was successful, set the file path
                if ($uploaded) {
                    $data['bin_image'] = $bin_path;  // Save the correct bin image path
                }
            }

            if ($request->hasFile('bin_barcode_img')) {
                $bin_bar_image = $request->file('bin_barcode_img');
                $bin_bar_imageName = time() . '_' . $bin_bar_image->getClientOriginalName(); 
                $bin_bar_path = "uploads/warehouse_file/{$bin_bar_imageName}";
                
                // Upload the barcode image to DigitalOcean Spaces using 'put' method
                $uploaded = Storage::disk('spaces')->put($bin_bar_path, file_get_contents($bin_bar_image), ['visibility' => 'public']);
                
                // If upload was successful, set the file path
                if ($uploaded) {
                    $data['bin_barcode_img'] = $bin_bar_path;  // Save the correct barcode image path
                }
            }

            $binlocation = BinLocation::create($data);

            // Commit the transaction
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Bin location created successfully',
                'result' => $binlocation
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exception
            return response()->json([
                'status' => 422,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Handle general exception, rollback transaction
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    } catch (\Exception $e) {
        // Handle outer try-catch for database transaction issues
        return response()->json([
            'status' => 500,
            'error' => 'Transaction failed: ' . $e->getMessage()
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
