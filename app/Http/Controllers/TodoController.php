<?php 
namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index()
    {
        // Fetch all todos from the database
        return response()->json(Todo::all());
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|string|max:255',  
            'completed' => 'boolean',  
        ]);

        // Create and save the new Todo
        $todo = Todo::create([
            'title' => $validated['title'],
            'completed' => $validated['completed'] ?? false,  
        ]);

        return response()->json($todo, 201);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'completed' => 'boolean',
        ]);
        
        // Update the Todo
        $todo->update($validated);
        
        return response()->json($todo);
    }

    public function destroy($id)
    {
        Todo::findOrFail($id)->delete();
        return response()->json(['message' => 'Todo deleted successfully']);
    }
}
