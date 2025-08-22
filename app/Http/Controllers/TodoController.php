<?php 

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Employee; 
use App\Models\Department;

class TodoController extends Controller
{
    public function index()
    {
        // Fetch all todos from the database and return as JSON response
        return response()->json(Todo::all(), Response::HTTP_OK);
    }
    // TodoController.php
    public function store(Request $request)
    {
    // Validate incoming data
    $validatedData = $request->validate([
        'task' => 'required|string|max:255',
        'assigned_to' => 'nullable|exists:employees,id', // Ensure it's an employee ID
        'department_id' => 'nullable|exists:departments,id',
    ]);

    // Create the task with validated data
    $task = Todo::create([
        'task' => $validatedData['task'],
        'assigned_to' => $validatedData['assigned_to'], // Assign the employee ID
        'department_id' => $validatedData['department_id'], 
    ]);
    return response()->json(['task' => $task], 201);  // Return the created task
    }

    // Edit button into to-do list 
     public function update(Request $request, $id)
    {
        // Fetch the Todo from the database
        $todo = Todo::findOrFail($id);
        
        // Validate the incoming request data
        $validated = $request->validate([
            'task' => 'required|string|max:255',
            'completed' => 'nullable|boolean',
        ]);
        
        // Update the Todo with validated data
        $todo->update($validated);
        
        return response()->json($todo, Response::HTTP_OK);
    }
    // Delete  button  function  into to-do list 
    public function destroy($id)
    {
        // Check if the Todo exists before trying to delete
        $todo = Todo::find($id);

        if (!$todo) {
            // If Todo not found, return a 404 response
            return response()->json(['message' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }
        // Delete the Todo
        $todo->delete();

        return response()->json(['message' => 'Todo deleted successfully'], Response::HTTP_OK);
    }
    // Add  button into to-do list 
    public function addTodo(Request $request)
    {
        // Validate the incoming request
         $validated = $request->validate([
        'task' => 'required|string|max:255',
    ]);

    // Create a new task
    $todo = Todo::create([
        'task' => $validated['task'],
        'completed' => false, 
    ]);

    // Return the created task in the response
    return response()->json($todo, 201);
    }

 public function assignTask(Request $request, $taskId)
{
    // Validate the incoming request
    $validated = $request->validate([
        'employee_id' => 'required|exists:employees,id',  // Ensures the employee_id exists in the employee table
    ]);

    // Find the task by ID
    $task = Todo::find($taskId);

    if (!$task) {
        return response()->json(['message' => 'Task not found'], 404);
    }

    // Assign the task to the employee
    $task->assigned_to = $validated['employee_id'];
    $task->save();

    return response()->json(['message' => 'Task assigned successfully']);
}

 
    public function fetchDropdownData()
    {
        // Get all employees and departments from the database
        $employees = Employee::all();
        $departments = Department::all();

        // Return data in a structured format for the frontend
        return response()->json([
            'employees' => $employees,
            'departments' => $departments
        ]);
    }
    public function getEmployeeTasks($employeeId)
    {
    // Fetch tasks assigned to the given employee
        $tasks = Todo::where('assigned_to', $employeeId)->get();
        return response()->json($tasks);
   }
}