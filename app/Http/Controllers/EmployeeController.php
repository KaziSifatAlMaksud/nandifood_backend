<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Warehouse;
use App\Models\EmployeeNotes;
 use Illuminate\Support\Facades\Storage;
 use Illuminate\Support\Facades\DB;
use App\Models\Positions;
use App\Models\BinStatus;
use App\Models\BinStorageType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
class EmployeeController extends Controller

{

 public function index(Request $request)
    {
        try {
            // Initialize the query
            $query = Employee::query();
            $id = $request->input('id');

            // Handle search input (search by name or employee_id)
            $search = $request->input('search');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('id', 'LIKE', "%{$search}%");
                });
            }

            // Apply pagination with a default limit
            $limit = $request->input('limit', 10); // Default limit set to 10
            $employeesPaginated = $query->paginate($limit);

            // Transform each employee to include the full URLs for images
            $employeesTransformed = $employeesPaginated->getCollection()->map(function ($employee) {
                $employee->img1 = $employee->img1 ? Storage::disk('spaces')->url($employee->img1) : null;
                $employee->img2 = $employee->img2 ? Storage::disk('spaces')->url($employee->img2) : null;
                $employee->img3 = $employee->img3 ? Storage::disk('spaces')->url($employee->img3) : null;
                return $employee;
            });

            // Update the pagination collection
            $employeesPaginated->setCollection($employeesTransformed);

            return response()->json([
                'status' => 200,
                'message' => 'OK.',
                'result' => [
                    'data' => $employeesPaginated,
                ],
            ]);
        } catch (\Exception $e) {
            // Catch any exceptions and return an error response
            return response()->json([
                'status' => '500',
                'message' => 'An error occurred: ' . $e->getMessage(),
                'result' => [
                    'data' => [],
                ],
            ]);
        }
    }



    public function store(Request $request)
    {
        $employee = new Employee();
        $employee->first_name = $request->first_name;
        $employee->country = $request->country;
        $employee->position_id = $request->position_id;
        $employee->warehouse_id = $request->warehouse_id;
        $employee->middle_name = $request->middle_name;
        $employee->last_name = $request->last_name;
        $employee->email = $request->email;
        $employee->off_phone = $request->off_phone;
        $employee->phone = $request->phone;
        $employee->status = $request->status;
        $employee->address1 = $request->address1;
        $employee->address2 = $request->address2;
        $employee->city = $request->city;
        $employee->state = $request->state;
        $employee->zip_code = $request->zip_code;
        $employee->certificates1 = $request->certificates1;
        $employee->certificates2 = $request->certificates2;
        $employee->certificates3 = $request->certificates3;
        $employee->certificates4 = $request->certificates4;
        $employee->eff_date = $request->eff_date;
        $employee->end_date = $request->end_date;
        $employee->start_date = $request->start_date;
        $employee->last_update = now()->format('Y-m-d H:i:s');
        $employee->update_by = $request->update_by;

        // Handle image uploads
        $uploadedImages = [];

        if ($request->hasFile('img1')) {
            $file = $request->file('img1');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = "uploads/warehouse_image/{$fileName}";
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), 'public');

            if ($uploaded) {
                $uploadedImages['img1'] = $path;
            } else {
                return response()->json([
                    'status' => 500,
                    'error' => 'Failed to upload img1 to DigitalOcean Spaces',
                ], 500);
            }
        }

        if ($request->hasFile('img2')) {
            $file = $request->file('img2');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = "uploads/warehouse_image/{$fileName}";
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), 'public');

            if ($uploaded) {
                $uploadedImages['img2'] = $path;
            } else {
                return response()->json([
                    'status' => 500,
                    'error' => 'Failed to upload img2 to DigitalOcean Spaces',
                ], 500);
            }
        }

        if ($request->hasFile('img3')) {
            $file = $request->file('img3');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = "uploads/warehouse_image/{$fileName}";
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), 'public');

            if ($uploaded) {
                $uploadedImages['img3'] = $path;
            } else {
                return response()->json([
                    'status' => 500,
                    'error' => 'Failed to upload img3 to DigitalOcean Spaces',
                ], 500);
            }
        }

        // If images were uploaded, assign their paths to the employee model
        if (isset($uploadedImages['img1'])) {
            $employee->img1 = $uploadedImages['img1'];
        }
        if (isset($uploadedImages['img2'])) {
            $employee->img2 = $uploadedImages['img2'];
        }
        if (isset($uploadedImages['img3'])) {
            $employee->img3 = $uploadedImages['img3'];
        }

        // Save the employee
         $employee->save();

        $employee_info = Employee::where('first_name', $request->first_name)
        ->where('email', $request->email)
        ->orderBy('last_update', 'desc')
        ->first();

         
        // Return a successful response
        return response()->json([
            'status' => 200,
            'message' => 'Employee created successfully.',
            'result' => [
                'data' => $employee_info
            ],
        ]);
    }

    public function get_all_notes($id)
    {
        // Retrieve employee notes for the given employee ID
        $employee_notes = EmployeeNotes::where('employee_id', $id)->get();

        // Map over the notes and handle attachments properly
        $employee_notes->map(function ($note) {
            if ($note->file_path) {
                $note->file = Storage::disk('spaces')->url($note->file_path);
                $note->file_name = basename($note->file_path);
            } else {
                $note->file = null; // If there's no file, set it to null
            }
            return $note;
        });

        // Return the response with the retrieved notes
        return response()->json([
            'status' => 200,
            'message' => 'Employee Notes retrieved successfully.',
            'result' => [
                'data' => $employee_notes
            ],
        ]);
    }



   public function edit($id)
    {
        $employee = Employee::with('notes')->find($id);
        if (!$employee) {
            return response()->json([
                'status' => 404,
                'message' => 'Employee not found.',
                'result' => [
                    'data' => []
                ]
            ]);
        }
        $employee->position_name = Positions::find($employee->position_id)->position_name;
        $warehouse = Warehouse::find($employee->warehouse_id);
        $employee->warehouse_name = $warehouse ? $warehouse->warehouse_name : null;
        $employee->img1 = $employee->img1 ? Storage::disk('spaces')->url($employee->img1) : null;
        $employee->img2 = $employee->img2 ? Storage::disk('spaces')->url($employee->img2) : null;
        $employee->img3 = $employee->img3 ? Storage::disk('spaces')->url($employee->img3) : null;



            // Process the notes to include file URLs and file names
        $employee->notes->map(function ($note) {
            if ($note->file_path) {
                $note->file = Storage::disk('spaces')->url($note->file_path);
                $note->file_name = basename($note->file_path);
            } else {
                $note->file = null; // If there's no file, set it to null
                $note->file_name = null; // No file name if no file exists
            }
            return $note;
        });


        // Return the employee details
        return response()->json([
            'status' => 200,
            'message' => 'Employee retrieved successfully.',
            'result' => [
                'data' => $employee
            ],
        ]);
    }


    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        // Check if the employee exists
        if (!$employee) {
            return response()->json([
                'status' => 404,
                'message' => 'Employee not found.',
                'result' => ['data' => []],
            ]);
        }

        // Update employee's fields
        $employee->id = $request->id;
        $employee->first_name = $request->first_name;
        $employee->country = $request->country;
        $employee->position_id = $request->position;
        $employee->warehouse_id = $request->warehouse_id;
        $employee->middle_name = $request->middle_name;
        $employee->last_name = $request->last_name;
        $employee->email = $request->email;
        $employee->off_phone = $request->off_phone;
        $employee->phone = $request->phone;
        $employee->status = $request->status;
        $employee->address1 = $request->address1;
        $employee->address2 = $request->address2;
        $employee->city = $request->city;
        $employee->state = $request->state;
        $employee->zip_code = $request->zip_code;
        $employee->certificates1 = $request->certificates1;
        $employee->certificates2 = $request->certificates2;
        $employee->certificates3 = $request->certificates3;
        $employee->certificates4 = $request->certificates4;
        $employee->eff_date = $request->eff_date;
        $employee->end_date = $request->end_date;
        $employee->start_date = $request->start_date;
        $employee->last_update = now()->format('Y-m-d H:i:s');
        $employee->update_by = $request->update_by;

        // Handle image updates
        $imageFields = ['img1', 'img2', 'img3'];
        foreach ($imageFields as $imageField) {
            if ($request->hasFile($imageField)) {
                $uploadedPath = $this->handleImageUpload($request->file($imageField), $employee->$imageField);
                if ($uploadedPath) {
                    $employee->$imageField = $uploadedPath;
                } else {
                    return response()->json([
                        'status' => 500,
                        'error' => "Failed to upload $imageField to DigitalOcean Spaces",
                    ], 500);
                }
            }
        }
        $employee->save();
        return response()->json([
            'status' => 200,
            'message' => 'Employee updated successfully.',
            'result' => ['data' => $employee],
        ]);
    }


    public function employee_notes_store(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|string|max:11',
                'file_path' => 'required|file|mimes:pdf,png,jpg,jpeg',
                'note_date' => 'nullable|string', 
                'file_description' => 'nullable|string|max:255',
            ]);
        
            DB::beginTransaction();
            
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $fileName =  $file->getClientOriginalName(); 
                $path = "uploads/employee_notes/{$fileName}";
                $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);
                if ($uploaded) {
                    $validated['file_path'] = $path; 
                } else {
                    throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
                }
            }

            $employeeNote  = EmployeeNotes::create($validated);
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Employee Notes created successfully.',
                'result' => [
                    'data' => $employeeNote
                ],
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

    public function employee_notes_delete($id)
    {
        try {
            $employeeNote = EmployeeNotes::findOrFail($id);
            if ($employeeNote->file_path) {
                $filePath = $employeeNote->file_path;
                if (Storage::disk('spaces')->exists($filePath)) {
                    Storage::disk('spaces')->delete($filePath);
                }
            }

            $employeeNote->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Employee Note deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        $employee = Employee::find($id);

        // Check if the employee exists
        if (!$employee) {
            return response()->json([
                'status' => 404,
                'message' => 'Employee not found.',
                'result' => [
                    'data' => []
                ]
            ]);
        }

        // Delete the employee
        $employee->delete();

        // Return a successful response
        return response()->json([
            'status' => 200,
            'message' => 'Employee deleted successfully.',
            'result' => [
                'data' => []
            ]
        ]);
    }

    private function handleImageUpload($file, $oldPath = null)
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = "uploads/warehouse_image/{$fileName}";
        $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), 'public');

        if ($uploaded) {
            if ($oldPath) {
                Storage::disk('spaces')->delete($oldPath);
            }
            return $path;
        }

        return null;
    }

    

    public function get_position()
    {
        $positions = Positions::all();
        return response()->json([
            'status' => 200,
            'message' => 'Positions retrieved successfully.',
            'result' => [
                'data' => $positions
            ],
        ]);
    }


    public function get_employee_name(){
    
        $employee = Employee::select('id', 'first_name','middle_name', 'last_name')->get(); // Fetch only id and first_name columns

        return response()->json([
        'status' => 200,
        'message' => 'Employee name fetched successfully.',
        'result' => [
            'data' => $employee
            ],
        ]);
    }


    

}
