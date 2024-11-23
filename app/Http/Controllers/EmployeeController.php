<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

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
    

}
