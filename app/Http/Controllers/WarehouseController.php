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


class WarehouseController extends Controller
{
    // Fetch all warehouse records
    // public function index()
    // {
    //     $warehouses = Warehouse::select([
    //         'id',
    //         'warehouse_name',
    //         'country',
    //         'state',
    //         'city',
    //         'status',
    //         'zip_code',
    //         'address1',
    //         'address2',
    //         'email',
    //         'phone',
    //         'warehouse_contact',
    //         'warehouse_capacity_in_kg',
    //     ])->get();

    //     $warehouses = $warehouses->map(function ($warehouse) {
    //             $totals  = BinLocation::calculateTotalVolumeForWarehouse($warehouse->id);
    //             $warehouse->volume_m3 = $totals['total_volume'];
    //             $warehouse->total_storage_capacity = $totals['total_storage_capacity_slp'];
    //             return $warehouse;
    //     });


    //     return response()->json([
    //         'status' => '200',
    //         'message' => 'Ok',
    //         'result'=>[
    //             'warehouses' =>  $warehouses
    //         ]
    //     ]);
    // }

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

    // Handle search functionality
    $search = $request->input('search'); // Space-separated values
    if ($search) {
        $terms = explode(' ', $search); // Split by spaces
        foreach ($terms as $term) {
            $query->where('warehouse_name', 'LIKE', "%{$term}%"); // Adjust column as needed
        }
    }

    // Handle pagination
    $limit = $request->input('limit', 5); // Default limit to 10
    $warehousesPaginated = $query->paginate($limit); // Get paginated result

    // Apply additional logic to each warehouse while keeping pagination
    $warehouses = $warehousesPaginated->getCollection()->map(function ($warehouse) {
        $totals = BinLocation::calculateTotalVolumeForWarehouse($warehouse->id);
        $warehouse->volume_m3 = $totals['total_volume'];
        $warehouse->total_storage_capacity = $totals['total_storage_capacity_slp'];
        return $warehouse;
    });

    // Update the paginated collection
    $warehousesPaginated->setCollection($warehouses);

    // Return response with pagination
    return response()->json([
        'status' => '200',
        'message' => 'Ok',
        'result' => $warehousesPaginated, // Contains data + pagination metadata
    ]);
}


    public function warehouse_compliance()
    {
          $warehouseattachment = WarehouseAttachment::all();
            return response()->json([
            'status' => '200',
            'message' => 'Ok',
            'result'=>$warehouseattachment,
        ]);
    }    

    // Fetch a single warehouse by ID
    public function show($id)
    {
        $warehouse = Warehouse::find($id);

        if ($warehouse) {
            return response()->json($warehouse);
        } else {
            return response()->json(['status' => '404',
            'message' => 'Error..!!',
           ]);
        }
    }

    // Store a new warehouse record
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
            'warehouse_name' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'country' => 'required|string|max:25',
            'state' => 'required|string|max:25',
            'city' => 'required|string|max:25',
            'zip_code' => 'required|string|max:20',
            'email' => 'required|email|max:255', 
            'phone' => 'nullable|string|max:255', 
            'address2' => 'nullable|string|max:255',
            'warehouse_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:255',
            'eff_date' => 'nullable|date', 
            'loc_work_week' => 'nullable|integer', 
            'work_week_days' => 'nullable|string|max:50', 
            'warehouse_manager' => 'nullable|string|max:255',
            'warehouse_supervisor' => 'nullable|string|max:255',
            'bus_hours_open' => 'nullable|string|max:10', 
            'bus_hours_close' => 'nullable|string|max:10',
            'status' => 'nullable|string|max:50',
        ]);
            DB::beginTransaction();
               $warehouse = Warehouse::create($validated);
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
    
    public function warehouse_attachment_store(Request $request)
    {
        try {
                $validated = $request->validate([
                'type' => 'required|integer', 
                'warehouse_id' => 'required|string|max:11',
                'file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:204800',
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
                $validated['file'] = $filePath;
            }
            $warehouse = WarehouseAttachment::create($validated);
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
    // Update an existing warehouse record
    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }

        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            // Add other validation rules here as per your requirements
        ]);

        // Update the warehouse record
        $warehouse->update([
            'name' => $request->name,
            'location' => $request->location,
            // Update other fields here
        ]);

        // Return the updated warehouse
        return response()->json([
            'status' => '200',
            'message' => 'Warehouse updated successfully',
            'warehouse' => $warehouse
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


    public function export() 
    {
        $slugDate = Str::slug(date('Y-m-d')); 
        $fileName = "{$slugDate}_warehouseList.xlsx";
        return Excel::download(new WarehouseExport, $fileName);
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
