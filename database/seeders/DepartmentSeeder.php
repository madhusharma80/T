<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = ['HR', 'Engineering', 'Sales', 'Marketing'];

        foreach ($departments as $department) {
            if (!DB::table('departments')->where('name', $department)->exists()) {
                DB::table('departments')->insert(['name' => $department]);
            }
        }
    }
}
