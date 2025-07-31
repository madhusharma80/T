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
    if (auth()->check()) {
        $user = auth()->user();
        dd($user);  // Dump user info to verify authentication
    } else {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}

}



