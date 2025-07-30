<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Task;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create some departments
        $dept1 = Department::create(['department_name' => 'HR']);
        $dept2 = Department::create(['department_name' => 'IT']);
        $dept3 = Department::create(['department_name' => 'Finance']);
        

        // Create some users
        $user1 = User::create(['name' => 'Demo', 'email' => 'Demo@example.com', 'password' => bcrypt('password')]);
        $user2 = User::create(['name' => 'Rim', 'email' => 'Rim@example.com', 'password' => bcrypt('password')]);

        // Create some tasks and assign to users and departments
        Task::create([
            'task_name' => 'Task 1',
            'description' => 'Description for task 1',
            'due_date' => '2025-08-01',
            'status' => 'pending',
            'user_id' => $user1->user_id,
            'department_id' => $dept1->department_id
        ]);
        
        Task::create([
            'task_name' => 'Task 2',
            'description' => 'Description for task 2',
            'due_date' => '2025-08-05',
            'status' => 'in-progress',
            'user_id' => $user2->user_id,
            'department_id' => $dept2->department_id
        ]);
    }
}
