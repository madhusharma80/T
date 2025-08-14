<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    // Define the relationship between Department and Employees
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    
     public function todos()
    {
        return $this->hasMany(Todo::class, 'department_id');
    }
}


