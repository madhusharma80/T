<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Designation;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Method to fetch departments, designations, and employees for dropdowns
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

    // Method to fetch all employees (for displaying employee list)
    public function fetchEmployees()
    {
        // Fetch all employees and return them as JSON
        $employees = Employee::with('department', 'designation')->get();

        return response()->json($employees);
    }

    // Method to add or update a new employee
    // Method to add a new employee
public function addEmployee(Request $request)
{
    // Validate the incoming request
    $validatedData = $request->validate([
        'email' => 'required|email',
        'department_id' => 'required|exists:departments,id',
        'designation_id' => 'required|exists:designations,id',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
    ]);

    // Create a new employee
    $employee = Employee::create($validatedData);

    // Load department and designation details
    $employee->load('department', 'designation'); // This ensures department and designation are included

    // Return the employee with department and designation
    return response()->json(['employee' => $employee], 200);  // Send back the full employee object
}

}
