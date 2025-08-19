<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['task', 'assigned_to', 'department_id'];
    
    // Relationship to fetch tasks assigned to this employee
   
    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function assignedDepartment()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function getEmployeeTasks($employeeId)
    {
    // Fetch tasks assigned to the given employee from the todo table
    $tasks = Todo::where('assigned_to', $employeeId)->get();

    return response()->json($tasks);
    }
    public function employee()
    {
    return $this->belongsTo(Employee::class, 'assigned_to');
    }
}