<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    /**
     * Get the tasks for the department.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class); // A department can have many tasks
    }
}
