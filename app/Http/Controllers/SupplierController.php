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

            try {
                $maxId = Supplier::max('id'); 
                $newSupplierId = $maxId + 1; 
                $newSupplierNo = 'C' . str_pad($newSupplierId, 4, '0', STR_PAD_LEFT);
                while (Supplier::where('supplier_no', $newSupplierNo)->exists()) {
                    $newSupplierId++; 
                    $newSupplierNo = 'S' . str_pad($newSupplierId, 4, '0', STR_PAD_LEFT);  // Generate new supplier_no
                }
                $data = $request->all();
                $data['supplier_no'] = $newSupplierNo;  
                if ($request->hasFile('img')) {
                    $img = $request->file('img');
                    $imgName = time() . '_' . $img->getClientOriginalName();
                    $imgPath = "uploads/supplier/{$imgName}";
                    $uploaded = Storage::disk('spaces')->put($imgPath, file_get_contents($img), ['visibility' => 'public']);
                    if ($uploaded) {
                        $data['img'] = $imgPath; 
                    }
                }
                $supplier = Supplier::create($data);
                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Supplier created successfully',
                    'result' => $supplier
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
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


    public function supplier_update(Request $request, $id)
    {
        DB::beginTransaction(); 

        try {
            // Find the Supplier record by ID
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

    public function get_supplier_all_notes($id)
    {
        $supplier_notes = SupplierNote::where('id', $id)->get();
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
                'data' => $supplier_notes
            ],
        ]);
    }



    public function supplier_notes_store(Request $request)
    {
        try {
            $validated = $request->validate([
                'supplier_id' => 'required|string|max:11',
                'file_path' => 'required|file|mimes:pdf,png,jpg,jpeg',
                'note_date' => 'nullable|string',
                'file_description' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();
            $supplierInfo = null;
            $SupplierInfo = Supplier::where('id', $validated['supplier_id'])->first();

            if ($SupplierInfo) {
                $supplierInfo = $SupplierInfo;
                $supplierInfo->notes = $request->file_description;
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



}
