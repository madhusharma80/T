<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',  // Foreign key to Department table
        'designation_id', // Foreign key to Designation table
        'assigned_to',    // Employee assigned to (e.g. Manager, Team Lead)
        'email',
        'name',
        'status',         // Active/Inactive status or other flags
    ];

    // Relationship with User (One to One)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Department (Many to One)
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relationship with Designation (Many to One)
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}
