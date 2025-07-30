<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Designation;

class DesignationSeeder extends Seeder
{
    public function run()
    {
        Designation::create(['name' => 'Manager']);
        Designation::create(['name' => 'Developer']);
        Designation::create(['name' => 'Analyst']);
    }
}




