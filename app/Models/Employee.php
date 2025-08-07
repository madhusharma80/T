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

    // Define the relationship for the employee that this employee is "assigned to" (e.g., a manager)
    public function assignedTo()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }
}
