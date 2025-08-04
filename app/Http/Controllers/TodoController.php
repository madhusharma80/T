<?php 

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TodoController extends Controller
{
    public function index()
    {
        // Fetch all todos from the database and return as JSON response
        return response()->json(Todo::all(), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|string|max:255',  
            'completed' => 'nullable|boolean',  // Make completed optional but boolean
        ]);

        // Create and save the new Todo
        $todo = Todo::create([
            'title' => $validated['title'],
            'completed' => $validated['completed'] ?? false,  // Default to false if not provided
        ]);

        return response()->json($todo, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        // Fetch the Todo from the database
        $todo = Todo::findOrFail($id);
        
        // Validate the incoming request data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'completed' => 'nullable|boolean',
        ]);
        
        // Update the Todo with validated data
        $todo->update($validated);
        
        return response()->json($todo, Response::HTTP_OK);
    }

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
}
