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
              return response()->json([
                'status' => 200,
                'message' => 'OK.',
                'result' => $employeesPaginated
            ]);
        } catch (\Exception $e) {
            // Catch any exceptions and return an error response
            return response()->json([
                'status' => '500',
                'message' => 'An error occurred: ' . $e->getMessage(),
                'result' => []
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
        $employee->last_update = $request->last_update;
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

        // Return a successful response
        return response()->json([
            'status' => 200,
            'message' => 'Employee created successfully.',
            'result' => $employee
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
            'result' => $employee_notes
        ]);
    }


   public function edit($id)
    {
        $employee = Employee::with('position','notes')->find($id);
        if (!$employee) {
            return response()->json([
                'status' => 404,
                'message' => 'Employee not found.',
                'result' => []
            ]);
        }

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
            'result' => $employee
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
                'result' => []
            ]);
        }

        // Update the employee's fields with the incoming request data
        $employee->id = $request->id;
        $employee->first_name = $request->first_name;
        $employee->country_id = $request->country;
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
        $employee->last_update = $request->last_update;
        $employee->update_by = $request->update_by;
        $employee->img1 = $request->img1;
        $employee->img2 = $request->img2;
        $employee->img3 = $request->img3;

        // Save the updated employee
        $employee->save();

        // Return a successful response
        return response()->json([
            'status' => 200,
            'message' => 'Employee updated successfully.',
            'result' => $employee
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
                'result' => $employeeNote ,
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

    public function destroy($id)
    {
        $employee = Employee::find($id);

        // Check if the employee exists
        if (!$employee) {
            return response()->json([
                'status' => 404,
                'message' => 'Employee not found.',
                'result' => []
            ]);
        }

        // Delete the employee
        $employee->delete();

        // Return a successful response
        return response()->json([
            'status' => 200,
            'message' => 'Employee deleted successfully.',
            'result' => []
        ]);
    }

    

    public function get_position()
    {
        $positions = Positions::all();
        return response()->json([
            'status' => 200,
            'message' => 'Positions retrieved successfully.',
            'result' => $positions
        ]);
    }


    

}
