<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        DB::table('departments')->insert([
            ['name' => 'HR'],
            ['name' => 'Engineering'],
            ['name' => 'Sales'],
            ['name' => 'Marketing']
        ]);
    }
}
