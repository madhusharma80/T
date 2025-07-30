<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{

public function index()
    {
        // Fetch all departments
        return Department::all(['id as department_id', 'department_name']);
    }


}
