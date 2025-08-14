<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['title', 'assigned_to', 'department_id'];

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function assignedDepartment()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}

