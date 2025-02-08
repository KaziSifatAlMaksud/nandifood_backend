<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Warehouse;
use App\Models\EmployeeNotes;
use App\Models\CustomerNote;
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

        // Transform the collection for each customer
        $customers->getCollection()->transform(function ($customer) {
            // Get the position name for each customer
            $customer->position_name = Positions::where('id', $customer->position)->value('position_name');

            // Add the image URL if it exists
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
                $maxId = Customer::max('id');  
                $newCustomerId = $maxId + 1;  
                $newCustomerNo = 'C' . str_pad($newCustomerId, 4, '0', STR_PAD_LEFT);
                while (Customer::where('customer_no', $newCustomerNo)->exists()) {
                    $newCustomerId++;  
                    $newCustomerNo = 'C' . str_pad($newCustomerId, 4, '0', STR_PAD_LEFT); 
                }

                $data = $request->all();
                $data['customer_no'] = $newCustomerNo; 

                if ($request->hasFile('img')) {
                    $img = $request->file('img');
                    $imgName = time() . '_' . $img->getClientOriginalName();
                    $imgPath = "uploads/customer/{$imgName}";
                    $uploaded = Storage::disk('spaces')->put($imgPath, file_get_contents($img), ['visibility' => 'public']);
                    if ($uploaded) {
                        $data['img'] = $imgPath;  // Add the image path to the data array
                    }
                }

                // Create the new customer
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
                // Handle any other exceptions
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
        $customer->position_name = Positions::where('id', $customer->position)->value('position_name');
        $customer->account_manager_name = Employee::where('id', $customer->account_manager)->value('first_name','middle_name', 'last_name');
        $customer->account_manager_name = Employee::where('id', $customer->category_manager)->value('first_name','middle_name', 'last_name');
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


    public function get_customer_all_notes($id)
    {
        $customer_notes = CustomerNote::where('id', $id)->get();
        $customer_notes->map(function ($note) {
            if ($note->file_path) {
                $note->file = Storage::disk('spaces')->url($note->file_path);
                $note->file_name = basename($note->file_path);
            } else {
                $note->file = null;
            }
            return $note;
        });
        return response()->json([
            'status' => 200,
            'message' => 'Customer Notes retrieved successfully.',
            'result' => [
                'data' => $customer_notes
            ],
        ]);
    }


    public function customer_notes_store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|string|max:11',
                'file_path' => 'required|file|mimes:pdf,png,jpg,jpeg',
                'note_date' => 'nullable|string',
                'file_description' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();
            $customerInfo = null;
            $CustomerInfo = Customer::where('id', $validated['customer_id'])->first();

            if ($CustomerInfo) {
                $customerInfo = $CustomerInfo;
                $customerInfo->notes = $request->file_description;  // Optionally update customer notes
                $customerInfo->save();
            }

            // Handle file upload if a file is provided
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $fileName = $file->getClientOriginalName();
                $path = "uploads/customer_notes/{$fileName}";
                $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);
                if ($uploaded) {
                    $validated['file_path'] = $path;
                } else {
                    throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
                }
            }

            // Create the customer note
            $customerNote = CustomerNote::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => ($customerNote && $customerInfo)
                    ? 'Customer Notes and Customer updated successfully.'
                    : ($customerNote
                        ? 'Customer Notes created successfully.'
                        : ($customerInfo
                            ? 'Customer updated successfully.'
                            : 'No changes were made.'
                        )
                    ),
                'result' => [
                    'data' => $customerNote,
                    'customerInfo' => $customerInfo
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function customer_notes_delete($id)
    {
        try {
            $customerNote = CustomerNote::findOrFail($id);
            if ($customerNote->file_path) {
                $filePath = $customerNote->file_path;
                if (Storage::disk('spaces')->exists($filePath)) {
                    Storage::disk('spaces')->delete($filePath);
                }
            }

            $customerNote->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Customer Note deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
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
