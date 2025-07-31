<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
   public function fetchDropdownData()
{
       $departments = Department::all();
        $designations= Designation::all(); 
        $employees = Employee::all();   // You can use 'pluck' to get specific columns

        // Return the departments as a JSON response
        return response()->json($departments);
    }
}



