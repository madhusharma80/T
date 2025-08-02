<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        // Create Employee 1
        $employee1 = Employee::create([
            'email' => 'john.doe@example.com',
            'department_id' => 1,  // HR
            'designation_id' => 1,  // Manager
        ]);

        // Create Employee 2
        $employee2 = Employee::create([
            'email' => 'jane.smith@example.com',
            'department_id' => 2,  // Engineering
            'designation_id' => 2,  // Developer
        ]);

        // Update 'assigned_to' relationships
        $employee1->assigned_to = $employee2->id; // Assign Employee 2 to Employee 1
        $employee1->save();

        $employee2->assigned_to = $employee1->id; // Assign Employee 1 to Employee 2
        $employee2->save();
    }
}
