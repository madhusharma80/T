<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        Employee::create([
            'email' => 'john.doe@example.com',
            'department_id' => 1,  // HR
            'designation_id' => 1,  // Manager
            'assigned_to' => 'Jane Smith'
        ]);

        Employee::create([
            'email' => 'jane.smith@example.com',
            'department_id' => 2,  // Engineering
            'designation_id' => 2,  // Developer
            'assigned_to' => 'John Doe'
        ]);
    }
}

