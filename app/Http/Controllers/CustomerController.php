<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Warehouse;
use App\Models\EmployeeNotes;
 use Illuminate\Support\Facades\DB;
use App\Models\Positions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Exports\EmployeeExport;
use Maatwebsite\Excel\Facades\Excel;



class CustomerController extends Controller
{
   public function customer_list(Request $request)
{
    $id = $request->input('id');
    $limit = (int) $request->input('limit', 5); // Default limit is 5
    $page = (int) $request->input('page', 1);  
    $query = Customer::query();

    if ($id) {
        $query->where('id', $id);
    }
    $customers = $query->paginate($limit, ['*'], 'page', $page);

    // Transform the collection
    $customers->getCollection()->transform(function ($customer) {
        $customer->img = $customer->img ? Storage::disk('spaces')->url($customer->img) : null;
        return $customer;
    });

    return response()->json([
        'status' => 200,
        'message' => 'Customer list retrieved successfully',
        'result' => $customers
    ]);
}


    public function customer_store(Request $request)
    {
        try {
            // Begin database transaction
            DB::beginTransaction();

            try {
                $data = $request->all();

                // Handle file upload for 'img' field (customer image)
                if ($request->hasFile('img')) {
                    $img = $request->file('img');
                    $imgName = time() . '_' . $img->getClientOriginalName(); 
                    $imgPath = "uploads/customer/{$imgName}";
                    $uploaded = Storage::disk('spaces')->put($imgPath, file_get_contents($img), ['visibility' => 'public']);
                    if ($uploaded) {
                        $data['img'] = $imgPath;  
                    }
                }

                // Create the customer record
                $customer = Customer::create($data);

                // Commit the transaction
                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Customer created successfully',
                    'result' => $customer
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



    // Show a specific customer

    public function customer_show($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->img = $customer->img ? Storage::disk('spaces')->url($customer->img) : null;

        if (!$customer) {
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        // Return the customer with the updated image URL
        return response()->json([
            'status' => 200,
            'message' => 'Customer retrieved successfully.',
            'result' => [
                'data' => $customer
            ],
        ]);
    }


    public function customer_update(Request $request, $id)
    {
        DB::beginTransaction(); 

        try {
            // Find the Customer record by ID
            $customer = Customer::find($id);
            if (!$customer) {
                return response()->json([
                    'status' => 404,
                    'success' => false,
                    'message' => 'Customer not found.',
                ], 404);
            }
            $customer->fill($request->except(['img']));
            if ($request->hasFile('img')) {
                $img = $request->file('img');

                if ($img->isValid()) {
                    if (!empty($customer->img) && Storage::disk('spaces')->exists($customer->img)) {
                        Storage::disk('spaces')->delete($customer->img);
                    }
                    $imgName = time() . '_' . $img->getClientOriginalName(); 
                    $imgPath = "uploads/customer/{$imgName}";
                    Storage::disk('spaces')->put($imgPath, file_get_contents($img), ['visibility' => 'public']);
                    $customer->img = $imgPath;
                } else {
                    return response()->json([
                        'status' => 400,
                        'success' => false,
                        'message' => "Invalid image upload for img!",
                    ], 400);
                }
            }
            $customer->save();
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Customer update successful.',
                'result' => $customer,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'An error occurred during the update process.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Delete a customer
    public function customer_destroy($id)
    {
         $customer = Customer::findOrFail($id);

        // Check if the country exists
        if (!$customer) {
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => ' Customer is not found.',
            ], 404);
        }
            $customer->delete();

        // Return a success response
            return response()->json([
                'status' => '200',
                'message' => 'Customer deleted successfully'
            ], 200);
    }
}
