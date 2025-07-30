<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Fetch all tasks for display
public function index(Request $request)
{
    $tasks = Task::with(['user', 'department'])->paginate(10);
    return response()->json($tasks);
}

public function store(Request $request)
{
    $task = Task::create($request->all());
    return response()->json($task, 201);
}

public function update(Request $request, $id)
{
    $task = Task::findOrFail($id);
    $task->update($request->all());
    return response()->json($task);
}

public function destroy($id)
{
    $task = Task::findOrFail($id);
    $task->delete();
    return response()->json(null, 204);
}

}
