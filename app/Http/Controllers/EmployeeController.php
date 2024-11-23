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
            $id = $request->input('id');
    
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
            //  return response()->json([
            // 'status' => 200,
            // 'message' => $id ? 'Filtered by warehouse ID' : 'Success',
            // 'result' => [
            //     'data' => $binLocationPaginated->items(), // Current page items
            //     'total' => $binLocationPaginated->total(), // Total number of items
            //     'per_page' => $binLocationPaginated->perPage(), // Items per page
            //     'current_page' => $binLocationPaginated->currentPage(), // Current page number
            //     'last_page' => $binLocationPaginated->lastPage(), // Last page number
            //     'links' => $binLocationPaginated->toArray()['links'], // Pagination links
            //     ]
            // ]);
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
