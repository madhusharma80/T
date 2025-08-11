<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    // Define the relationship between Employee and Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Define the relationship with Designation (if applicable)
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

     public function fetchEmployees()
    {
        $employees = Employee::all(); // Fetch all employees

        // Return the employees as JSON
        return response()->json($employees);
    }
    // app/Models/Employee.php
public function tasks()
{
    return $this->hasMany(Task::class);
}

}

