<?php

namespace App\Http\Controllers;
 
use App\Models\Todo;
use Illuminate\Http\Request;
 
class TodoController extends Controller
{
    public function index()
    {
        return Todo::with(['user:id,name', 'department:id,name'])->get();
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required', 'email' => 'required|email',
            'description' => 'required',
            'assigned_to' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
        ]);
        return Todo::create($request->all());
    }
 
    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->update($request->all());
        return $todo;
    }
 
    public function destroy($id)
    {
        return Todo::destroy($id);
    }
}