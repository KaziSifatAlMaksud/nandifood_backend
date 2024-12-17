<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\BinLocation;
use App\Exports\WarehouseExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\WarehouseAttachment;
use App\Models\BinStatus;
use App\Models\BinStorageType;
use App\Models\Uom_type;
use App\Models\Country;
use App\Models\Employee;
  use Illuminate\Support\Facades\Storage;

class WarehouseController extends Controller
{
 public function index(Request $request)
{
    // Select only required columns to optimize query performance
    $query = Warehouse::select([
        'id',
        'warehouse_name',
        'country',
        'state',
        'city',
        'status',
        'zip_code',
        'address1',
        'address2',
        'email',
        'phone',
        'warehouse_contact',
        'warehouse_capacity_in_kg',
    ]);
    $search = $request->input('search');
    if ($search) {
        $terms = explode(' ', $search);
        foreach ($terms as $term) {
            $query->where('warehouse_name', 'LIKE', "%{$term}%");
        }
    }
    $limit = $request->input('limit', 5);
    $warehousesPaginated = $query->paginate($limit);
    $warehouses = $warehousesPaginated->getCollection()->map(function ($warehouse) {
        $totals = BinLocation::calculateTotalVolumeForWarehouse($warehouse->id);
        $warehouse->volume_m3 = $totals['total_volume'];
        $warehouse->total_storage_capacity = $totals['total_storage_capacity_slp'];
        return $warehouse;
    });
    $warehousesPaginated->setCollection($warehouses);
    return response()->json([
        'status' => '200',
        'message' => 'Ok',
        'result' => $warehousesPaginated,
    ]);
}

public function country()
{
    // Fetch distinct countries from the 'country' table
    $distinctCountries = Country::all();
        return response()->json([
        'status' => '200',
        'message' => 'Ok',
        'result' => $distinctCountries,
    ]);
}

public function getCountries()
{
    $distinctCountries = Country::selectRaw('MIN(id) as id, country as name')
        ->groupBy('country')
        ->get();

    return response()->json([
        'status' => '200',
        'message' => 'Ok',
        'result' => [
            'data' => $distinctCountries
        ]
    ]);
}


    public function getStates($countryName)
    {
        // Fetch distinct states for the specified country
    $distinctStates = Country::where('country', $countryName)
        ->selectRaw('MIN(id) as id, state as name')
        ->groupBy('state')
        ->get();



        // Check if there are any states
        if ($distinctStates->isEmpty()) {
            return response()->json([
                'status' => '404',
                'message' => 'No states found for the specified country.',
            ]);
        }

        return response()->json([
            'status' => '200',
            'message' => 'Ok',
            'result' => [
                'data' => $distinctStates
            ]
        ]);
    }



public function getCities($stateName)
{
      $distinctCities = Country::where('state', $stateName)
        ->selectRaw('MIN(id) as id, city as name')
        ->groupBy('city')
        ->get();

    // Check if cities were found
    if ($distinctCities->isEmpty()) {
        return response()->json([
            'status' => '404',
            'message' => 'No cities found for the specified state.',
        ]);
    }

    return response()->json([
        'status' => '200',
        'message' => 'Ok',
        'result' => [
                    'data' =>$distinctCities
        ]
    ]);
}


public function getEmployee($warehouse_id)
{    // Fetch the employees associated with the warehouse
    $employees = Employee::where('warehouse_id', $warehouse_id)->get();

    return response()->json([
        'status' => '200',
        'message' => 'Ok',
        'result' => [
            'data' => $employees,
        ],
    ]);
}

public function getAttachment($warehouse_id)
{    // Fetch the employees associated with the warehouse
    $attachment = WarehouseAttachment::where('warehouse_id', $warehouse_id)->get();

    return response()->json([
        'status' => '200',
        'message' => 'Ok',
        'result' => [
            'data' => $attachment,
        ],
    ]);
}

public function getBinLocation($warehouse_id)
{    // Fetch the employees associated with the warehouse
    $binLocation = BinLocation::where('warehouse_id', $warehouse_id)->get();

    return response()->json([
        'status' => '200',
        'message' => 'Ok',
        'result' => [
            'data' => $binLocation,
        ],
    ]);
}






public function warehouse_compliance(Request $request)
{
    // Optionally, limit the records or paginate them based on the request
    $limit = $request->input('limit', 10); // Default to 10 items per page

    // Fetch records with pagination or all records (adjust as needed)
    $warehouseattachments = WarehouseAttachment::paginate($limit); // Use pagination for better performance

    // Loop through each attachment and generate file URL
    $warehouseattachments->getCollection()->transform(function ($attachment) {

        if ($attachment->file) {
            $attachment->file = Storage::disk('spaces')->url($attachment->file);
        } else {
            $attachment->file = null; // If there's no file, set it to null
        }
        return $attachment;
    });

    // Return a response with the data
    return response()->json([
        'status' => 200,
        'message' => 'Ok',
        'result' => $warehouseattachments,
    ]);
}


  
// public function show($id)
// {
//     // Find the warehouse by ID with related models
//     $warehouse = Warehouse::with('binLocations') // Add other relationships as needed
//         ->find($id);

//     // Check if the warehouse exists
//     if ($warehouse) {
//         // Calculate the totals if necessary
//         $totals = BinLocation::calculateTotalVolumeForWarehouse($warehouse->id);
//         $warehouse->volume_m3 = $totals['total_volume'];
//         $warehouse->total_storage_capacity = $totals['total_storage_capacity_slp'];

//         // Return the response in the desired format with all information
//         return response()->json([
//             'status' => '200',
//             'message' => 'Ok',
//             'result' => [
//                 'data' => $warehouse, // The warehouse with all loaded relationships
//             ],
//         ]);
//     } else {
//         // If the warehouse is not found, return an error message
//         return response()->json([
//             'status' => '404',
//             'message' => 'Error: Warehouse not found!',
//         ]);
//     }
// }


public function show($id)
{
    // Retrieve the warehouse by its ID, including related data
    $warehouse = Warehouse::with(['binLocations', 'warehouse_attachment', 'employee'])->find($id);

    if ($warehouse) {
        // Generate the URL for the warehouse image, if it exists
        $warehouse->wh_image = $warehouse->wh_image ? Storage::disk('spaces')->url($warehouse->wh_image) : null;

        // Process binLocations to concatenate the fields and add 'full_bin_location'
        $binLocations = $warehouse->binLocations->map(function ($binLocation) {
            // Ensure all binLocation fields are available before concatenation
            $binLocation->full_bin_location =  $binLocation->zone_number ."-". 
                                              $binLocation->section_number ."-". 
                                              $binLocation->aisle_number ."-".
                                              $binLocation->rack_number ."-".
                                              $binLocation->shelf_number ."-".
                                              $binLocation->bin_number;
            $totals = BinLocation::calculateTotalVolume($binLocation->id);
            $binLocation->volume_m3 = $totals;
            return $binLocation;
        });

        // Return the data, including binLocations with the full_bin_location
        return response()->json([
            'status' => 200,
            'message' => 'Warehouse retrieved successfully.',
            'result' => [
                'data' => $warehouse,
                'binLocations' => $binLocations,  // Include processed binLocations
            ],
        ]);
    } else {
        return response()->json([
            'status' => 404,
            'message' => 'Error: Warehouse not found.',
        ]);
    }
}



public function warehouse_name(){
    $warehouses = Warehouse::select('id', 'warehouse_name as name')->get();

    return response()->json([
        'status' => '200',
        'message' => 'Ok',
        'result' => [
            'data' => $warehouses
        ]
    ]);
}

public function store(Request $request)
{
    try {
        // Validate the request
        $validated = $request->validate([
            'entity' => 'nullable|string|max:255',
            'warehouse_name' => 'nullable|string|max:255',
            'global_default_warehouse' => 'nullable|string|max:10',
            'warehouse_capacity_in_kg' => 'nullable|string|max:10',
            'address1' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:25',
            'state' => 'nullable|string|max:25',
            'city' => 'nullable|string|max:25',
            'zip_code' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'warehouse_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:255',
            'eff_date' => 'nullable|string',
            'loc_work_week' => 'nullable|string',
            'work_week_days' => 'nullable|string|max:50',
            'warehouse_manager' => 'nullable|string|max:255',
            'warehouse_supervisor' => 'nullable|string|max:255',
            'bus_hours_open' => 'nullable|string|max:10',
            'bus_hours_close' => 'nullable|string|max:10',
            'status' => 'nullable|string|max:50',
            'wh_image' => 'nullable|mimes:jpg,jpeg,png,pdf|max:200048',
        ]);

        DB::beginTransaction();  // Start transaction

        // Initialize filePath variable
        $filePath = null;

        // Check if the request has a file
        if ($request->hasFile('wh_image')) {
            $file = $request->file('wh_image');
            $fileName = time() . '_' . $file->getClientOriginalName(); 
            $path = "uploads/warehouse_image/{$fileName}";
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), 'public');

            if ($uploaded) {
                $filePath = $path;  
            } else {
                return response()->json([
                    'status' => 500,
                    'error' => 'Failed to upload file to DigitalOcean Spaces',
                ], 500);
            }
        }
        $warehouseData = $validated;
        if ($filePath) {
            $warehouseData['wh_image'] = $filePath;
        }
        $warehouse = Warehouse::create($warehouseData);

        DB::commit();  // Commit transaction

       $newWarehouse = Warehouse::where('warehouse_name', $warehouseData['warehouse_name'])
            ->where('email', $warehouseData['email'])
            ->first();

        if (!$newWarehouse) {
            throw new \Exception('Failed to retrieve the newly created warehouse.');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Warehouse created successfully',
            'result' => $newWarehouse,
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        return response()->json([
            'status' => 422,
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        // Rollback the transaction in case of any error
        DB::rollBack();

        // Return a response with the exception message
        return response()->json([
            'status' => 500,
            'error' => $e->getMessage(),
        ], 500);
    }
}



    // Store a new warehouse record
// public function store(Request $request)
// {
//     try {
//         // Validate the request (including the file upload)
//         $validated = $request->validate([
//             'entity' => 'nullable|string|max:255',
//             'warehouse_name' => 'nullable|string|max:255',
//             'global_default_warehouse'  => 'nullable|string|max:10',
//             'warehouse_capacity_in_kg' => 'nullable|string|max:10',
//             'address1' => 'nullable|string|max:255',
//             'country' => 'nullable|string|max:25',
//             'state' => 'nullable|string|max:25',
//             'city' => 'nullable|string|max:25',
//             'zip_code' => 'nullable|string|max:20',
//             'email' => 'nullable|email|max:255', 
//             'phone' => 'nullable|string|max:255', 
//             'address2' => 'nullable|string|max:255',
//             'warehouse_contact' => 'nullable|string|max:255',
//             'emergency_phone' => 'nullable|string|max:255',
//             'eff_date' => 'nullable|string', 
//             'loc_work_week' => 'nullable|string', 
//             'work_week_days' => 'nullable|string|max:50', 
//             'warehouse_manager' => 'nullable|string|max:255',
//             'warehouse_supervisor' => 'nullable|string|max:255',
//             'bus_hours_open' => 'nullable|string|max:10', 
//             'bus_hours_close' => 'nullable|string|max:10',
//             'status' => 'nullable|string|max:50',
//             'wh_image' => 'nullable|mimes:jpg,jpeg,png,pdf|max:200048'
//         ]);

//         DB::beginTransaction();

//         $filePath = null;
//         if ($request->hasFile('wh_image')) {
//             $filePath = $request->file('wh_image')->store('uploads/warehouse_image', 'public');
//         }
//         $warehouseData = $validated;
//         if ($filePath) {
//             $warehouseData['wh_image'] = $filePath;
//         }

//         $warehouse = Warehouse::create($warehouseData);

//         DB::commit();

//         return response()->json([
//             'status' => 200,
//             'message' => 'Warehouse created successfully',
//             'result' => $warehouse,
//         ]);
//     } catch (\Illuminate\Validation\ValidationException $e) {
//         return response()->json([
//             'status' => 422,
//             'errors' => $e->errors(),
//         ], 422);
//     } catch (\Exception $e) {
//         DB::rollBack();

//         return response()->json([
//             'status' => 500,
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }

public function update(Request $request, $warehouseId)
{
    // Find warehouse by ID
    $warehouse = Warehouse::find($warehouseId);

    // Check if the warehouse exists
    if (!$warehouse) {
        return response()->json([
            'status' => '404',
            'message' => 'Error: Warehouse not found!',
        ], 404);
    }

    // Validate the incoming request
    $request->validate([
        'wh_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048000', // Validate image
        // Add validation for other fields if needed
    ]);

    // Initialize the image path variable
    $imagePath = $warehouse->wh_image; // Keep the old image path if no new image is uploaded

    // Check if the 'wh_image' file is present in the request
    if ($request->hasFile('wh_image')) {
        $file = $request->file('wh_image');

        // Validate if the file is a valid image
        if ($file->isValid()) {
            // Delete the old image from DigitalOcean Spaces if it exists
            if ($warehouse->wh_image) {
                // Remove the old image from DigitalOcean Spaces
                Storage::disk('spaces')->delete($warehouse->wh_image);
            }

            // Generate a new file name to avoid conflicts
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = "uploads/warehouse_image/{$fileName}";

            // Upload the new image to DigitalOcean Spaces
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);

            // If upload was successful, set the image path
            if ($uploaded) {
                $imagePath = $path; // Update the image path
            } else {
                return response()->json([
                    'status' => '400',
                    'message' => 'Error: File upload to DigitalOcean Spaces failed!',
                ], 400);
            }
        } else {
            return response()->json([
                'status' => '400',
                'message' => 'Error: File upload is not valid!',
            ], 400);
        }
    }

    // Update all warehouse fields from the request, including the image path
    $warehouse->fill($request->all());
    $warehouse->wh_image = $imagePath; // Ensure the correct image path is saved

    // Save the updated warehouse
    $warehouse->save();

    // Return success response with the updated warehouse data
    return response()->json([
        'status' => '200',
        'message' => 'Warehouse updated successfully.',
        'result' => [
            'data' => $warehouse, // Return the updated warehouse data
        ],
    ]);
}


// public function update(Request $request, $warehouseId)
// {
//     // Find warehouse by ID
//     $warehouse = Warehouse::find($warehouseId);

//     // Check if the warehouse exists
//     if (!$warehouse) {
//         return response()->json([
//             'status' => '404',
//             'message' => 'Error: Warehouse not found!',
//         ], 404);
//     }

//     // Validate the incoming request
//     $request->validate([
//         'wh_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048000', // Validate image
//         // Add validation for other fields if needed
//     ]);

//     // Initialize the image path variable
//     $imagePath = $warehouse->wh_image; // Keep the old image path if no new image is uploaded

//     // Check if the 'wh_image' file is present in the request
//     if ($request->hasFile('wh_image')) {
//         $file = $request->file('wh_image');

//         // Validate if the file is a valid image
//         if ($file->isValid()) {
//             // Delete the old image if it exists
//             if ($warehouse->wh_image) {
//                 // Remove the old image from storage
//                 Storage::disk('public')->delete($warehouse->wh_image);
//             }

//             // Store the new image and get the path
//             $imagePath = $file->store('uploads/warehouse_image', 'public');
//         } else {
//             return response()->json([
//                 'status' => '400',
//                 'message' => 'Error: File upload is not valid!',
//             ], 400);
//         }
//     }

//     // Update all warehouse fields from the request, including the image path
//     $warehouse->fill($request->all());
//     $warehouse->wh_image = $imagePath; // Ensure the correct image path is saved

//     // Save the updated warehouse
//     $warehouse->save();

//     // Return success response with the updated warehouse data
//     return response()->json([
//         'status' => '200',
//         'message' => 'Warehouse updated successfully.',
//         'result' => [
//             'data' => $warehouse, // Return the updated warehouse data
//         ],
//     ]);
// }

 

    
//     public function warehouse_attachment_store(Request $request)
// {
//     try {
//         // Validate the incoming request data
//         $validated = $request->validate([
//             // 'type' => 'required|integer', 
//             'warehouse_id' => 'required|string|max:11',
//             'type' => 'required|integer',
//             'file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:20480000',
//             'created_by' => 'nullable|string|max:11', 
//             'updated_by' => 'nullable|string|max:11',
//             'date_uploaded' => 'nullable|date', 
//             'description' => 'nullable|string|max:255',
//         ]);
      
//         DB::beginTransaction();
        
//         // Check if the request has a file
//         if ($request->hasFile('file')) {
//             // Store the file in the 'public' disk and get the file path
//             $filePath = $request->file('file')->store('uploads/attachment', 'public');
//             $validated['file'] = $filePath;  // Add the file path to the validated data
//         }

//         // Create the warehouse attachment record
//         $warehouse = WarehouseAttachment::create($validated);

//         DB::commit();

//         // Return a success response
//         return response()->json([
//             'status' => 200,
//             'message' => 'Warehouse attachment created successfully.',
//             'result' => $warehouse,
//         ]);
//     } catch (\Illuminate\Validation\ValidationException $e) {
//         // Return a custom response with validation errors
//         return response()->json([
//             'status' => 422,
//             'errors' => $e->errors(),
//         ], 422);
//     } catch (\Exception $e) {
//         // Roll back the transaction in case of an exception
//         DB::rollBack();

//         // Return a response with the exception message
//         return response()->json([
//             'status' => 500,
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }

public function warehouse_attachment_store(Request $request)
{
    try {
        // Validate the incoming request data
        $validated = $request->validate([
            'warehouse_id' => 'required|string|max:11',
            'type' => 'required|integer',
            'file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:20480000',
            'created_by' => 'nullable|string|max:11', 
            'updated_by' => 'nullable|string|max:11',
            'date_uploaded' => 'nullable|date', 
            'description' => 'nullable|string|max:255',
        ]);
      
        DB::beginTransaction();
        
        // Check if the request has a file
        if ($request->hasFile('file')) {
            // Get the uploaded file
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName(); 
            $path = "uploads/warehouse_attachment/{$fileName}";
            
            // Upload the file to DigitalOcean Spaces using 'put' method
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);
            
            // If the upload was successful, store the file path
            if ($uploaded) {
                $validated['file'] = $path;  // Add the file path to the validated data
            } else {
                throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
            }
        }

        // Create the warehouse attachment record
        $warehouse = WarehouseAttachment::create($validated);

        // Commit the transaction
        DB::commit();

        // Return a success response
        return response()->json([
            'status' => 200,
            'message' => 'Warehouse attachment created successfully.',
            'result' => $warehouse,
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Return a custom response with validation errors
        return response()->json([
            'status' => 422,
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        // Roll back the transaction in case of an exception
        DB::rollBack();

        // Return a response with the exception message
        return response()->json([
            'status' => 500,
            'error' => $e->getMessage(),
        ], 500);
    }
}


 
    public function warehouse_attachment_destroy($id)
    {
        $warehouse = WarehouseAttachment::find($id);

        if (!$warehouse) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }

        // Delete the warehouse
        $warehouse->delete();

        // Return a success message
        return response()->json([
            'status' => '200',
            'message' => 'Warehouse deleted successfully'
        ]);
    }



    

    // Delete a warehouse record
    public function destroy($id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }

        // Delete the warehouse
        $warehouse->delete();

        // Return a success message
        return response()->json([
            'status' => '200',
            'message' => 'Warehouse deleted successfully'
        ]);
    }


    public function edit($id)
{
    // Retrieve the warehouse by its ID
    $warehouse = Warehouse::findOrFail($id);

    if ($warehouse) {
        // If the warehouse has an image, generate its URL
        $warehouse->wh_image = $warehouse->wh_image ? Storage::disk('spaces')->url($warehouse->wh_image) : null;

        return response()->json([
            'status' => '200',
            'message' => 'Success',
            'result' => [
                'data' => $warehouse,
            ],
        ]);
    } else {
        return response()->json([
            'status' => '404',
            'message' => 'Error: Warehouse not found!',
        ]);
    }
}



    public function export() 
    {
        $slugDate = Str::slug(date('Y-m-d')); 
        $fileName = "{$slugDate}_warehouseList.xlsx";
        return Excel::download(new WarehouseExport, $fileName);
    }


    public function exportCsv()
    {
        // Export using the WarehouseExport class, specifying CSV format
        return Excel::download(new WarehouseExport, 'warehouses_' . date('Y-m-d') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function bin_storage_type(){
    $bin_status = BinStorageType::all();
        return response()->json($bin_status);
    }

    public function bin_status(){
        $bin_storage_type = BinStatus::all();
        return response()->json($bin_storage_type);
    }

    public function uom_type(){
        $uom_type = Uom_type::all();
        return response()->json($uom_type);
    }


    //import excel file
    // public function warehouse_excel(Request $request)
    // {
    //     $request->validate([
    //         'import_file' => 'required|mimes:xlsx,xls',
    //     ]);

    //     $path = $request->file('file')->getRealPath();
    //     $data = \Excel::import(new WarehouseImport, $path);

    //     return response()->json([
    //         'status' => '200',
    //         'message' => 'Warehouse imported successfully',
    //         'data' => $data
    //     ]);
    // }
}
