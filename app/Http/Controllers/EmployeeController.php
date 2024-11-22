<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{

    public function index(Request $request, $id = null)
    {
        try {
            // Initialize the query
            $query = Employee::query();
    
            // Filter by warehouse_id if $id is provided
            if ($id) {
                $query->where('default_warehouse', $id);
            }
    
            // Handle search input (search by name or employee_id)
            $search = $request->input('search');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('employee_id', 'LIKE', "%{$search}%");
                });
            }
    
            // Apply pagination with a default limit
            $limit = $request->input('limit', 10); // Default limit set to 10
            $employeesPaginated = $query->paginate($limit);
    
            // Return the paginated response with links
            return response()->json([
                'status' => '200',
                'message' => $id ? 'Employees found for warehouse.' : 'All employees retrieved.',
                'result' => [
                    'employees' => $employeesPaginated->items(), // Current page items
                    'total' => $employeesPaginated->total(),    // Total number of items
                    'per_page' => $employeesPaginated->perPage(), // Items per page
                    'current_page' => $employeesPaginated->currentPage(), // Current page number
                    'last_page' => $employeesPaginated->lastPage(), // Last page number
                    'links' => $employeesPaginated->toArray()['links'], // Pagination links
                ]
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
