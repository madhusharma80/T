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
}