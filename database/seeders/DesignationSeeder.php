<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Designation;

class DesignationSeeder extends Seeder
{
    public function run()
    {
        $designations = ['Manager', 'Developer', 'Analyst'];

        foreach ($designations as $designation) {
            if (!Designation::where('name', $designation)->exists()) {
                Designation::create(['name' => $designation]);
            }
        }
    }
}
