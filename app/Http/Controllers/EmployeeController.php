<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Designation;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function fetchDropdownData()
    {
        // Fetch departments and designations
        $departments = Department::all();
        $designations = Designation::all();

        // Fetch employees with related department and designation data
        $employees = Employee::with('department', 'designation') 
            ->get()
            ->map(function ($employee) {
                $employee->name = $employee->first_name . ' ' . $employee->last_name;
                
                // Return the employee object with the full name and relevant details
                return [
                    'id' => $employee->id,  
                    'name' => $employee->name, 
                    'email' => $employee->email, 
                    'department' => $employee->department,
                    'designation' => $employee->designation, 
                ];
            });

        // Return departments, designations, and the employee data in the response
        return response()->json([
            'departments' => $departments,
            'designations' => $designations,
            'employees' => $employees,  
        ]);
    }

      public function fetchEmployees()
    {
        $employees = Employee::all(); // Fetch all employees

        // Return the employees as JSON
        return response()->json($employees);
    }
}





