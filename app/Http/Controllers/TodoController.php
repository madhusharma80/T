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
public function store(Request $request)
{
    // Log the incoming request data for debugging
    \Log::info('Received task:', ['task' => $request->task]);

    // Validate the incoming data
    $validated = $request->validate([
        'task' => 'required|string|max:255',  // Validate that 'task' is required
    ]);

    // Create a new todo entry with the validated task
    $todo = Todo::create([
        'task' => $request->task,  // Ensure the task is passed into the database
    ]);

    // Log the saved todo item for debugging
    \Log::info('Saved todo:', ['todo' => $todo]);

    // Return the created todo as JSON
    return response()->json($todo, 201);
}
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
        'completed' => false, // default to false
    ]);

    // Return the created task in the response
    return response()->json($todo, 201);
}
public function assignTask(Request $request, $id)
{
    $task = Todo::find($id);
    
    if ($task) {
        $task->assigned_to = $request->assigned_to;
        $task->department_id = $request->department_id;
        $task->save();
        
        return response()->json($task);
    }

    return response()->json(['message' => 'Task not found'], 404);
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


}