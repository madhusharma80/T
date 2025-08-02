<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
public function fetchDropdownData()
{
    $departments = Department::all();
    $designations = Designation::all();  
    $employees = Employee::with('department', 'designation')->get();  // Fetch employees with related data

    return response()->json([
        'departments' => $departments,
        'designations' => $designations,
        'employees' => $employees,
    ]);
}
}



