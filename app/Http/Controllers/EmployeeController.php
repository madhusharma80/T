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
// In EmployeeController.php
public function deleteEmployee($id)
{
    $employee = Employee::find($id);

    if (!$employee) {
        return response()->json(['message' => 'Employee not found.'], 404);
    }

    $employee->delete();  // Delete the employee from the database
    return response()->json(['message' => 'Employee deleted successfully.'], 200);
}

  public function getEmployeeEmails()
    {
        // Fetch only the necessary columns (id, first_name, last_name, email)
        $employees = Employee::select('id', 'email')->get();
        
        // Return employees data in JSON format
        return response()->json(['employees' => $employees]);
    }

public function assignTask(Request $request, $taskId)
{
    $request->validate([
        'assigned_to' => 'required|exists:employees,id',  // Ensure the selected employee exists
    ]);

    // Find the task by ID
    $task = Todo::find($taskId);

    if (!$task) {
        return response()->json(['error' => 'Task not found'], 404);
    }

    // Assign task to employee
    $task->assigned_to = $request->assigned_to;
    $task->save();

    // Fetch the assigned employee
    $employee = Employee::find($request->assigned_to);

    // Add the task to the employee's task list (optional)
    $employee->tasks()->attach($task);  // Or simply use $employee->tasks()->save($task);

    return response()->json(['message' => 'Task assigned successfully!', 'assigned_task' => $task]);
}


public function getEmployeesWithTasks()
{
    // Fetch employees along with their assigned tasks
    $employees = Employee::with('tasks')->get();  // Assuming Employee has a tasks relationship

    return response()->json($employees);
}

}
