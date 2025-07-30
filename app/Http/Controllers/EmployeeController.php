<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Get departments (This can be fetched from a database table or be static)
    public function getDepartments()
    {
        $departments = ['HR', 'Engineering', 'Sales', 'Marketing']; // You can replace this with database fetch logic
        return response()->json($departments);
    }

    // Get designations (This can be fetched from a database table or be static)
    public function getDesignations()
    {
        $designations = ['Manager', 'Lead', 'Intern', 'Developer']; // Replace with DB fetch if needed
        return response()->json($designations);
    }

    // Get employees for the assigned_to dropdown (fetch from users table)
    public function getEmployees()
    {
        $employees = User::select('id', 'name', 'email')->get(); // Get user data
        return response()->json($employees);
    }
}
