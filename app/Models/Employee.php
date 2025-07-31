<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'email', 
        'department_id', 
        'designation_id', 
        'assigned_to'
    ];

    // Relationship with Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relationship with Designation
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}
