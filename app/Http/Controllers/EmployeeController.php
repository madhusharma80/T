<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Method to fetch dynamic employee data for the frontend
    public function getEmployeeData()
    {
        // Fetch data from the database
        $department = Department::all()->pluck('name'); // Get only the 'name' column
        $designation = Designation::all()->pluck('name'); // Get only the 'name' column
        $assignedTo = User::all()->pluck('name'); // Get only the 'name' column from users
        $employeeIds = Employee::all()->pluck('id'); // Get employee IDs
        $employeeEmails = Employee::all()->pluck('email'); // Get employee emails

        // Return the data in JSON format
        return response()->json([
            'departments' => $departments,
            'designations' => $designations,
            'assignedTo' => $assignedTo,
            'employeeIds' => $employeeIds,
            'employeeEmails' => $employeeEmails,
        ]);
    }
}
