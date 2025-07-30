<?php

// database/seeders/EmployeeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        Employee::create([
            'email' => 'john@example.com',
            'department' => 'HR',
            'designation' => 'Manager',
            'assigned_to' => 'John Doe'
        ]);

        Employee::create([
            'email' => 'jane@example.com',
            'department' => 'Engineering',
            'designation' => 'Lead',
            'assigned_to' => 'Jane Doe'
        ]);

        // Add more employees as needed
    }
}
