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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Exports\EmployeeExport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller

{

public function index(Request $request)
{
    try {
        // Initialize the query
        $query = Employee::query();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }
        $limit = $request->input('limit', 10); 
        $employeesPaginated = $query->orderBy('id', 'DESC')->paginate($limit);
        $employeesPaginated->getCollection()->transform(function ($employee) {
            $employee->img1 = $employee->img1 ? Storage::disk('spaces')->url($employee->img1) : null;
            $employee->img2 = $employee->img2 ? Storage::disk('spaces')->url($employee->img2) : null;
            $employee->img3 = $employee->img3 ? Storage::disk('spaces')->url($employee->img3) : null;
            $employee->position = $employee->position_id
                ? optional(Positions::find($employee->position_id))->position_name
                : "";

            return $employee;
        });

        return response()->json([
            'status' => 200,
            'message' => 'OK.',
            'result' => [
                'data' => $employeesPaginated,
            ],
        ]);
    } catch (\Exception $e) {
        // Return an error response in case of exception
        return response()->json([
            'status' => 500,
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
        $action = $request->action; 

        $isApprove = ($action == 'approve') ? 2 : 1;

        $employee->is_approved =  $isApprove;
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

        $employee_info = Employee::where('id', $id)->select('notes')->first();
        // Retrieve employee notes for the given employee ID
        $employee_notes = EmployeeNotes::where('employee_id', $id)->get();
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
                'data' => $employee_notes,
                'employee_info' => $employee_info
        ],
        ]);

    }



    public function show($id)
    {
        // Retrieve employee with related notes
       $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'status' => 404,
                'message' => 'Employee not found.',
                'result' => [
                    'data' => []
                ]
            ]);
        }

        // Use null-safe operator (PHP 8+) or ternary operator to prevent errors
        $employee->position_name = Positions::find($employee->position_id)?->position_name ?? null;
        
        $warehouse = Warehouse::find($employee->warehouse_id);
        $employee->warehouse_name = $warehouse?->warehouse_name ?? null;

        // Convert image paths to full URLs if they exist
        $employee->img1 = $employee->img1 ? Storage::disk('spaces')->url($employee->img1) : null;

        // Process notes safely
        // if ($employee->notes) {
        //     $employee->notes->map(function ($note) {
        //         if ($note->file_path) { 
        //             $note->file = Storage::disk('spaces')->url($note->file_path);
        //             $note->file_name = basename($note->file_path);
        //         } else {
        //             $note->file = null;
        //             $note->file_name = null;
        //         }
        //         return $note;
        //     });
        // }

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
        if (!$employee) {
            return response()->json([
                'status' => 404,
                'message' => 'Employee not found.',
                'result' => ['data' => []],
            ]);
        }

        $employee->fill($request->except('img1'));

        $action = $request->action; 
        $isApprove = ($action == 'approve') ? 2 : 1;
        $employee->is_approved =  $isApprove;
    
    
        if ($request->hasFile('img1')) {
            $file = $request->file('img1');
            $fileName = $file->getClientOriginalName();
            $path = "uploads/barcodes/{$fileName}";
    
            // Delete old image if exists
            if (!empty($employee->img1)) {
                Storage::disk('spaces')->delete($employee->img1);
            }
    
            // Upload new image to DigitalOcean Spaces
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);
    
            if ($uploaded) {
                $employee->img1 = $path;
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to upload img1 to DigitalOcean Spaces.',
                ], 500);
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
            // Validate incoming request
            $validated = $request->validate([
                'employee_id' => 'required|string|max:11',
                'file_path' => 'nullable|file|mimes:pdf,png,jpg,jpeg',
                'note_date' => 'nullable|string', 
                'file_description' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();

            // Fetch employee information
            $employeeInfo = null;
            $employeeNote = null;
            $EmployeeInfo = Employee::where('id', $validated['employee_id'])->first();

            if ($EmployeeInfo) {
                $employeeInfo = $EmployeeInfo;
                $employeeInfo->notes = $request->file_description;  // Optionally update employee notes
                $employeeInfo->save();
            }

            // Handle file upload if a file is provided
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $fileName = $file->getClientOriginalName();
                $path = "uploads/employee_notes/{$fileName}";
                $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);
                if ($uploaded) {
                    $validated['file_path'] = $path;
                    $employeeNote = EmployeeNotes::create($validated);
                } else {
                    throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
                }
                 
            }      

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => ($employeeNote && $employeeInfo)
                    ? 'Employee Notes and Employee updated successfully.'
                    : ($employeeNote
                        ? 'Employee Notes created successfully.'
                        : ($employeeInfo
                            ? 'Employee updated successfully.'
                            : 'No changes were made.'
                        )
                    ),
                'result' => [
                    'data' => $employeeNote,
                    'employeeInfo' => $employeeInfo
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

        if (!$employee) {
            return response()->json([
                'status' => 404,
                'message' => 'Employee not found.',
                'result' => ['data' => []]
            ]);
        }

        // Delete related Employee Notes first
        EmployeeNotes::where('employee_id', $id)->delete();

        // Delete the employee
        $employee->delete();

        // Return a successful response
        return response()->json([
            'status' => 200,
            'message' => 'Employee deleted successfully.',
            'result' => ['data' => []]
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

    public function get_employee_name()
    {
        $employees = DB::table('employee')
            ->join('positions', 'employee.position_id', '=', 'positions.id')
            ->select(
                'employee.id',
                'employee.first_name',
                'employee.middle_name',
                'employee.last_name',
                'positions.position_name'
            )
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Employee name fetched successfully.',
            'result' => [
                'data' => $employees
            ],
        ]);
    }


  public function employeeExport()
    {
        $fileName = now()->format('Y-m-d') . '_EmployeeList.xlsx';

        return Excel::download(new EmployeeExport, $fileName);
    }

}
