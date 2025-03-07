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
use App\Models\Supplier;
use App\Models\SupplierNote;
use App\Models\SupplierCategories;
use App\Models\CreditTerm;
use App\Models\CreditName;


class SupplierController extends Controller
{
  

    public function supplier_list(Request $request)
    {
        $id = $request->input('id');
        $limit = (int) $request->input('limit', 5);
        $page = (int) $request->input('page', 1);  

        $query = Supplier::query();
        if ($id) {
            $query->where('id', $id);
        }
        $suppliers = $query->paginate($limit, ['*'], 'page', $page);
        $suppliers->getCollection()->map(function ($supplier) {
            $supplier->supplier_category_name = SupplierCategories::where('id', $supplier->supplier_category)->value('category_name');
            return $supplier;
        });
        $suppliers->getCollection()->transform(function ($supplier) {
            $supplier->img = $supplier->img ? Storage::disk('spaces')->url($supplier->img) : null;
            return $supplier;
        });
        return response()->json([
            'status' => 200,
            'message' => 'Supplier list retrieved successfully',
            'result' => $suppliers
        ]);
    }


    public function supplier_show($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->img = $supplier->img ? Storage::disk('spaces')->url($supplier->img) : null;
        $supplier->position_name = Positions::where('id', $supplier->position)->value('position_name');
        $supplier->account_manager_name = Employee::where('id', $supplier->account_manager)
            ->selectRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name")
            ->value('full_name');
        $supplier->category_manager_name = Employee::where('id', $supplier->category_manager)
            ->selectRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name")
            ->value('full_name');
        if (!$supplier) {
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => 'Supplier not found.',
            ], 404);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Supplier retrieved successfully.',
            'result' => [
                'data' => $supplier
            ],
        ]);
    }

    

    public function supplier_store(Request $request)
    {
        try {
            // Begin database transaction
            DB::beginTransaction();

            $action = $request->action; 
            $isApprove = ($action == 'approve') ? 2 : 1;

            try {
                $maxId = Supplier::max('id'); 
                $newSupplierId = $maxId + 1; 
                $newSupplierNo = 'S' . str_pad($newSupplierId, 4, '0', STR_PAD_LEFT);

                // Ensure unique supplier_no
                while (Supplier::where('supplier_no', $newSupplierNo)->exists()) {
                    $newSupplierId++; 
                    $newSupplierNo = 'S' . str_pad($newSupplierId, 4, '0', STR_PAD_LEFT);
                }

                $data = $request->all();
                $data['is_approved'] = $isApprove; // Assign is_approve based on action
                $data['supplier_no'] = $newSupplierNo;
            

                // Handle image upload
                if ($request->hasFile('img')) {
                    $img = $request->file('img');
                    $imgName = time() . '_' . $img->getClientOriginalName();
                    $imgPath = "uploads/supplier/{$imgName}";
                    $uploaded = Storage::disk('spaces')->put($imgPath, file_get_contents($img), ['visibility' => 'public']);
                    if ($uploaded) {
                        $data['img'] = $imgPath;
                    }
                }

                // Create supplier
                $supplier = Supplier::create($data);
                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Supplier created successfully',
                    'result' => $supplier
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                DB::rollBack();
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Transaction failed: ' . $e->getMessage()
            ], 500);
        }
    }


    public function supplier_update(Request $request, $id)
    {
        DB::beginTransaction(); 

        try {
          
           $action = $request->action; 
           $isApprove = ($action == 'approve') ? 2 : 1;


            $supplier = Supplier::find($id);
            if (!$supplier) {
                return response()->json([
                    'status' => 404,
                    'success' => false,
                    'message' => 'Supplier not found.',
                ], 404);
            }

            $supplier->fill($request->except(['img']));
            if ($request->hasFile('img')) {
                $img = $request->file('img');
                if ($img->isValid()) {
                    // If the supplier already has an image, delete the old one from storage
                    if (!empty($supplier->img) && Storage::disk('spaces')->exists($supplier->img)) {
                        Storage::disk('spaces')->delete($supplier->img);
                    }

                    $imgName = time() . '_' . $img->getClientOriginalName(); 
                    // Define the path for the new image
                    $imgPath = "uploads/supplier/{$imgName}";

                    Storage::disk('spaces')->put($imgPath, file_get_contents($img), ['visibility' => 'public']);
                    // Update the supplier's image field with the new path
                    $supplier->img = $imgPath;
                } else {
                    return response()->json([
                        'status' => 400,
                        'success' => false,
                        'message' => "Invalid image upload for img!",
                    ], 400);
                }
            }
            $supplier->is_approved = $isApprove;
            $supplier->save();
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Supplier update successful.',
                'result' => $supplier,
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


    public function supplier_destroy($id)
    {
        try {
            // Find the Supplier record by ID
            $supplier = Supplier::findOrFail($id);

            // Delete the Supplier record
            $supplier->delete();

            // Return a success response
            return response()->json([
                'status' => 200,
                'message' => 'Supplier deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            // Return an error response in case of an exception
            return response()->json([
                'status' => 500,
                'error' => 'An error occurred while deleting the supplier: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function get_shipping_info($id)
    {
        $shipping_info = ShippingInfo::where('id', $id)->get();
        return response()->json([
            'status' => 200,
            'message' => 'Shipping Info retrieved successfully.',
            'result' => [
                'data' => $shipping_info
            ],
        ]);
    }

    public function get_supplier_all_notes($id, $type)
    {
        
        $supplier_notes = SupplierNote::where([
            ['supplier_id', $id],
            ['type', $type]
        ])->get();

       $supplier_node_info = new \stdClass();
        $notes = Supplier::where('id', $id)->value('notes');
        $notes2 = Supplier::where('id', $id)->value('notes2');

        $supplier_node_info->notes = $notes ?? '';  
        $supplier_node_info->notes2 = $notes2 ?? ''; 



        $supplier_notes->map(function ($note) {
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
            'message' => 'Supplier Notes retrieved successfully.',
            'result' => [
                'data' => $supplier_notes,
                'supplier_node_info' => $supplier_node_info
            ],
        ]);
    }



    public function supplier_notes_store(Request $request)
    {
        try {
            $validated = $request->validate([
                'supplier_id' => 'required|string|max:11',
                'file_path' => 'nullable|file|mimes:pdf,png,jpg,jpeg',
                'note_date' => 'nullable|string',
                'file_description' => 'nullable|string|max:255',
                'type' => 'required|integer',
            ]);

            DB::beginTransaction();
            $supplierInfo = "";
            $supplierNote = "";
            $SupplierInfo = Supplier::where('id', $validated['supplier_id'])->first();

            if ($SupplierInfo && $request->type == 1) {
                $supplierInfo = $SupplierInfo;
                $supplierInfo->notes = $request->file_description;
                $supplierInfo->save();
            }
            if ($SupplierInfo && $request->type == 2) {
                $supplierInfo = $SupplierInfo;
                $supplierInfo->notes2 = $request->file_description;
                $supplierInfo->save();
            }

            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $fileName = $file->getClientOriginalName();
                $path = "uploads/supplier_notes/{$fileName}";
                $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);
                if ($uploaded) {
                    $validated['file_path'] = $path;
                    $supplierNote = SupplierNote::create($validated);

                } else {
                    throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
                }
            }

          
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => ($supplierNote && $supplierInfo)
                    ? 'Supplier Notes and Notes updated successfully.'
                    : ($supplierNote
                        ? 'Supplier Notes created successfully.'
                        : ($supplierInfo
                            ? 'Notes updated successfully.'
                            : 'No changes were made.'
                        )
                    ),
                'result' => [
                    'data' => $supplierNote,
                    'supplierInfo' => $supplierInfo
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


    public function supplier_notes_delete($id)
    {
        try {
            $supplierNote = SupplierNote::findOrFail($id);
            if ($supplierNote->file_path) {
                $filePath = $supplierNote->file_path;
                if (Storage::disk('spaces')->exists($filePath)) {
                    Storage::disk('spaces')->delete($filePath);
                }
            }

            $supplierNote->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Supplier Note deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function supplier_category(){
        $supplier_categories = SupplierCategories::all();
        return response()->json([
            'status' => 200,
            'message' => 'Supplier categories retrieved successfully.',
            'result' => [
                'data' => $supplier_categories
            ],
        ]);
    }





    public function get_credit_terms($type, $cus_sup_id)
    {
        $creditTerm = CreditTerm::where('cus_sup_id', $cus_sup_id)
                                ->where('type', $type)
                                ->first();
        
        if ($creditTerm) {
            return response()->json([
                'status' => 200,
                'message' => 'Credit term retrieved successfully.',
                'result' => [
                    'data' => $creditTerm
                ],
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Credit term not found.',
            ]);
        }
    }


    public function get_credit_terms_name(){
        $credit_terms = CreditName::all();
        return response()->json([
            'status' => 200,
            'message' => 'Credit terms name retrieved successfully.',
            'result' => [
                'data' => $credit_terms
            ],
        ]);
    }
    
   
//   public function credit_terms_store(Request $request)
// {
//     $action = $request->action;

//     // Shared validation
//     $validated = $request->validate([
//         'credit_terms' => 'required|string|max:11',
//         'credit_type' => 'nullable|string|max:255',
//         'credit_limit' => 'nullable|string|max:255',
//         'credit_status' => 'nullable|string|max:255',
//         'cus_sup_id' => 'required|integer',
//         'notes' => 'nullable|string',
//         'type' => 'required|integer'
//     ]);

//     // Initialize the $fileData array to store file upload data
//     $fileData = [];

//     // File upload logic
//     if ($request->hasFile('file')) {
//         $file = $request->file('file');
//         $fileName = time() . '_' . $file->getClientOriginalName();
//         $path = "uploads/supplier_notes/{$fileName}";

//         // Upload the file to the cloud storage
//         $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);
//         if ($uploaded) {
//             // Store the file path and note date in the $fileData array
//             $fileData['file_path'] = $path;
//             $fileData['note_date'] = now(); // current date
            
//             // Success message for file upload
//             return response()->json([
//                 'status' => 200,
//                 'message' => 'File uploaded successfully!',
//                 'file_path' => $path
//             ]);
//         } else {
//             return response()->json([
//                 'status' => 500,
//                 'message' => 'File upload failed.',
//             ], 500);
//         }
//         if ($request->type == 1) {
//             $fileData['customer_id'] = $request->cus_sup_id;
//             $fileData['type'] = 2;
//             CustomerNote::create($fileData);
//         } elseif ($request->type == 2) {
//             $fileData['supplier_id'] = $request->cus_sup_id;
//             $fileData['type'] = 2;
//             SupplierNote::create($fileData);
//         }
//     }

//     // Handle the action regardless of whether the file was uploaded
//     switch ($action) {
//         case 'approved':
//             $validated['is_approve'] = 2;
//             break;

//         case 'save':
//             $validated['is_approve'] = 1;
//             break;

//         default:
//             return response()->json(['message' => 'Invalid request type!'], 400);
//     }

//     return $this->handleCreditTerm($validated, $request);
// }


public function credit_terms_store(Request $request)
{
        $action = $request->action;

        // Shared validation
        $validated = $request->validate([
            'credit_terms' => 'required|string|max:11',
            'credit_type' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|string|max:255',
            'credit_status' => 'nullable|string|max:255',
            'cus_sup_id' => 'required|integer',
            'notes' => 'nullable|string',
            'type' => 'required|integer'
        ]);

        // Initialize the $fileData array to store file upload data
        $fileData = [];

        // File upload logic
        if ($request->hasFile('file')) {

        
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = "uploads/supplier_notes/{$fileName}";

            // Upload the file to the cloud storage
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);
            if ($uploaded) {
                // Store the file path and note date in the $fileData array
                $fileData['file_path'] = $path;
                $fileData['note_date'] = now(); // current date
                $fileData['file_url'] = Storage::disk('spaces')->url($path);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'File upload failed.',
                ], 500);
            }
        }

        // Handle the action regardless of whether the file was uploaded
        switch ($action) {
            case 'approved':
                $validated['is_approve'] = 2;
                
                break;

            case 'save':
                $validated['is_approve'] = 1;
                break;

            default:
                return response()->json(['message' => 'Invalid request type!'], 400);
        }

        // Now we handle the credit terms and file upload together
        if ($request->type == 1) {
            $fileData['customer_id'] = $request->cus_sup_id;
            $fileData['type'] = 2;   
            $creditData = array_merge($validated, $fileData);
        
            
            if ($request->hasFile('file')) {     
                CustomerNote::create($creditData);
            }
        } elseif ($request->type == 2) {
                $fileData['supplier_id'] = $request->cus_sup_id;
                $fileData['type'] = 2;  
                $creditData = array_merge($validated, $fileData);
            
            
                if ($request->hasFile('file')) {
                SupplierNote::create($creditData);
            }
        }

        $this->handleCreditTerm($validated, $request);
        // Return a response with both the credit terms and file data
        return response()->json([
            'status' => 200,
            'message' => 'Credit terms and file uploaded successfully!',
            'credit_terms' => $validated,
            'file_data' => $fileData,
        ]);
    }

    private function handleCreditTerm($validated, $request)
    {
        $cus_sup_id = $request->cus_sup_id;
        $creditTerm = CreditTerm::where('cus_sup_id', $cus_sup_id)->first();

        if ($creditTerm) {
            $creditTerm->update($validated);
        } else {
            $creditTerm = CreditTerm::create($validated);
        }

        if ($request->type == 2) {  // Supplier
            $supplierInfo = Supplier::find($cus_sup_id);
            if ($supplierInfo) {
                $supplierInfo->credit_terms = $request->credit_limit;
                $supplierInfo->save();
            }
        } elseif ($request->type == 1) {  // Customer
            $customerInfo = Customer::find($cus_sup_id);
            if ($customerInfo) {
                $customerInfo->credit_terms = $request->credit_limit;
                $customerInfo->save();
            }
        }

        // Return response with credit term data
        return response()->json([
            'message' => 'Credit term handled successfully!',
            'data' => $creditTerm
        ]);
    }


  
}
