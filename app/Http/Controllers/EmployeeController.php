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
// function for  delete button which is place in the Employee task management  list 
public function deleteEmployee($id)
{
    $employee = Employee::find($id);

    if (!$employee) {
        return response()->json(['message' => 'Employee not found.'], 404);
    }

    $employee->delete();  // Delete the employee from the database
    return response()->json(['message' => 'Employee deleted successfully.'], 200);
}
// In EmployeeController.php
public function updateEmployee(Request $request, $id)
{
    // Fetch employee by ID
    $employee = Employee::findOrFail($id);

    // If email is updated, apply unique validation
    $emailValidation = $employee->email === $request->email
        ? 'required|email'
        : 'required|email|unique:employees,email';

    // Validate the request data
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => $emailValidation,
        'department_id' => 'required|exists:departments,id',
        'designation_id' => 'required|exists:designations,id',
    ]);

    // Update the employee details
    $employee->update($request->all());

    return response()->json($employee);
}


public function getEmployeeEmails()
    {
        // Fetch only the necessary columns (id, first_name, last_name, email)
        $employees = Employee::select('id', 'email')->get();
        
        // Return employees data in JSON format
        return response()->json(['employees' => $employees]);
    }

    
public function fetchEmployees(Request $request)
{
    // Paginate the results, limiting to 3 employees per page
    $employees = Employee::with('department', 'designation')
        ->paginate(4);  // Limit to 3 employees per page

    return response()->json($employees);
}
}