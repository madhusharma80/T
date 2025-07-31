<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        Employee::create([
            'email' => 'john@example.com',
            'department_id' => 1,  // HR
            'designation_id' => 1,  // Manager
            'assigned_to' => 'John Doe'
        ]);
        
        // Add more employees as needed
    }
}
