<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Add the fields you want to allow mass assignment
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'department_id',
        'designation_id',
        'task_id', // Include task_id if you're assigning tasks to employees
    ];

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

    public function tasks()
    {
         return $this->hasMany(Todo::class, 'assigned_to');
    }
}