<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\BinLocation;
use App\Exports\WarehouseExport;
use Maatwebsite\Excel\Facades\Excel;


class WarehouseController extends Controller
{
    // Fetch all warehouse records
    public function index()
    {
        $warehouses = Warehouse::select([
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
        ])->get();

        $warehouses = $warehouses->map(function ($warehouse) {
                $warehouse->volume = BinLocation::calculateTotalVolumeForWarehouse($warehouse->id);
                return $warehouse;
        });

        return response()->json([
            'status' => '200',
            'message' => 'Ok',
            'result'=>[
                'warehouses' =>  $warehouses
            ]
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
    // public function store(Request $request)
    // {
    //     // Validate the incoming request
    //    $data = validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'location' => 'required|string|max:255',
    //     ]);

    //     if($data = Warehouse::where('warehouse_name', $request->name)->first()){
    //         return response()->json([
    //             'status' => '400',
    //             'message' => 'Warehouse already exists',
    //         ], 400);
    //     }
    //     // Create the new warehouse
    //     $warehouse = Warehouse::create([
    //         'name' => $request->name,
    //         'location' => $request->location,
    //         // Add other fields here
    //     ]);

    //     // Return the newly created warehouse
    //     return response()->json([
    //         'status' => '201',
    //         'message' => 'Warehouse created successfully',
    //         'warehouse' => $warehouse
    //     ], 201);
    // }

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
        
        return Excel::download(new WarehouseExport, 'warehouseList.xlsx');
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
