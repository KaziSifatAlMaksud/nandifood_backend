<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
   public function index($id = null)
{

    try {
        if ($id) {
            // Query employees by warehouse_id if $id is provided
            $employees = Employee::where('default_warehouse', $id)->get();

            // Check if there are no employees found
            if ($employees->isEmpty()) {
                return response()->json([
                    'status' => '404',
                    'message' => 'No employees found for this warehouse.',
                    'result' => []
                ]);
            }

            return response()->json([
                'status' => '200',
                'message' => 'Employees found for warehouse.',
                'result' => [
                    'employees' => $employees
                ]
            ]);
        } else {
            // Fetch all employees if no warehouse_id is provided
            $employees = Employee::all();

            return response()->json([
                'status' => '200',
                'message' => 'All employees retrieved.',
                'result' => [
                    'employees' => $employees
                ]
            ]);
        }
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
