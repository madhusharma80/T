<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'completed', 'employee_id'];  // Added employee_id to fillable

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}

