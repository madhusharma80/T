<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
    
        $employee1 = Employee::create([
            'email' => 'john.doe@example.com',
             'first_name' => 'John',  
            'last_name' => 'Doe',    
            'department_id' => 1,  
            'designation_id' => 1, 
           
        ]);
      
        $employee2 = Employee::create([
            'email' => 'jane.smith@example.com',
             'first_name' => 'Jane',  
            'last_name' => 'Doe',    
            'department_id' => 2,  
            'designation_id' => 2,  
    
        ]);

        $employee3 = Employee::create([
            'email' => 'abc.@example.com',
             'first_name' => 'Abc',  
            'last_name' => 'Doe',  
            'department_id' => 3, 
            'designation_id' => 3,  
          
        ]);

        $employee4 = Employee::create([
            'email' => 'admin@example.com',
             'first_name' => 'Admine', 
            'last_name' => 'Doe',   
            'department_id' => 4, 
            'designation_id' => 4,  
            
        ]);

        $employee5 = Employee::create([
            'email' => 'david.white@example.com',
             'first_name' => 'David',  
            'last_name' => 'Doe',   
            'department_id' => 5,  
            'designation_id' => 5,  
        ]);

      
        $employee1->assigned_to = $employee2->id; 
        $employee1->save();

        $employee2->assigned_to = $employee1->id;
        $employee2->save();

        $employee3->assigned_to = $employee4->id; 
        $employee3->save();

        $employee4->assigned_to = $employee3->id; 
        $employee4->save();

        $employee5->assigned_to = $employee1->id; 
        $employee5->save();
    }
}
