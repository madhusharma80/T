<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;

class DropdownController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        $departments = Department::all();
        $designations = Designation::all();
        $assignedTo = Employee::all();

        return response()->json([
            'employees' => $employees,
            'departments' => $departments,
            'designations' => $designations,
            'assigned_to' => $assignedTo
        ]);
    }
}
