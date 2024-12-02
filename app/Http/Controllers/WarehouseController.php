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



public function warehouse_compliance(Request $request)
{
    // Optionally, limit the records or paginate them based on the request
    $limit = $request->input('limit', 10); // Default to 10 items per page

    // Fetch records with pagination or all records (adjust as needed)
    $warehouseattachments = WarehouseAttachment::paginate($limit); // Use pagination for better performance

    // Loop through each attachment and generate file URL
    $warehouseattachments->getCollection()->transform(function ($attachment) {

        if ($attachment->file) {
            $attachment->file = Storage::url($attachment->file);
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
    $warehouse = Warehouse::with('binLocations','employee','warehouse_attachment')->find($id);

    if ($warehouse) {
        // Generate the URL for the warehouse image, if it exists
        $warehouse->wh_image = $warehouse->wh_image ? Storage::url($warehouse->wh_image) : null;

        return response()->json([
            'status' => 200,
            'message' => 'Warehouse retrieved successfully.',
            'result' => [
                'data' => $warehouse,
            ],
        ]);
    } else {
        return response()->json([
            'status' => 404,
            'message' => 'Error: Warehouse not found.',
        ]);
    }
}





    // Store a new warehouse record
public function store(Request $request)
{
    try {
        // Validate the request (including the file upload)
        $validated = $request->validate([
            'entity' => 'nullable|string|max:255',
            'warehouse_name' => 'nullable|string|max:255',
            'global_default_warehouse'  => 'nullable|string|max:10',
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
            'wh_image' => 'nullable|mimes:jpg,jpeg,png,pdf|max:200048'
        ]);

        DB::beginTransaction();

        $filePath = null;
        if ($request->hasFile('wh_image')) {
            // Validate the file and store it
            $filePath = $request->file('wh_image')->store('uploads/warehouse_image', 'public');
        }
        $warehouseData = $validated;
        if ($filePath) {
            $warehouseData['wh_image'] = $filePath;
        }

        $warehouse = Warehouse::create($warehouseData);

        DB::commit();

        // Return a success response
        return response()->json([
            'status' => 200,
            'message' => 'Warehouse created successfully',
            'result' => $warehouse,
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Return a custom response with validation errors
        return response()->json([
            'status' => 422,
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        // Rollback the transaction in case of a general exception
        DB::rollBack();

        // Return a response with the exception message
        return response()->json([
            'status' => 500,
            'error' => $e->getMessage(),
        ], 500);
    }
}

    
public function update(Request $request, $id)
{
    $warehouse = Warehouse::find($id);

    if (!$warehouse) {
        return response()->json([
            'status' => '404',
            'message' => 'Error: Warehouse not found!',
        ], 404);
    }
    if ($request->hasFile('wh_image')) {
        $request->validate([
            'wh_image' => 'mimes:jpg,jpeg,png,pdf|max:200048', // Example validation
        ]);
        $filePath = $request->file('wh_image')->store('uploads/warehouse_image', 'public');
        $warehouse->wh_image = $filePath;
    }
    $warehouse->update($request->except('wh_image')); 
    return response()->json([
        'status' => '200',
        'message' => 'Warehouse updated successfully.',
        'result' => [
            'data' => $warehouse,
        ],
    ]);
}


    
    public function warehouse_attachment_store(Request $request)
{
    try {
        // Validate the incoming request data
        $validated = $request->validate([
            // 'type' => 'required|integer', 
            'warehouse_id' => 'required|string|max:11',
            'file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:20480000',
            'created_by' => 'nullable|string|max:11', 
            'updated_by' => 'nullable|string|max:11',
            'date_uploaded' => 'nullable|date', 
            'description' => 'nullable|string|max:255',
        ]);
      
        DB::beginTransaction();
        
        // Check if the request has a file
        if ($request->hasFile('file')) {
            // Store the file in the 'public' disk and get the file path
            $filePath = $request->file('file')->store('uploads/attachment', 'public');
            $validated['file'] = $filePath;  // Add the file path to the validated data
        }

        // Create the warehouse attachment record
        $warehouse = WarehouseAttachment::create($validated);

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
        $warehouse->wh_image = $warehouse->wh_image ? Storage::url($warehouse->wh_image) : null;

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
