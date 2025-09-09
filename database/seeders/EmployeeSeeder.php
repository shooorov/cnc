<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $designations_count = Designation::count();
        for ($i = 1; $i <= $designations_count * 2; $i++) {
            DB::table('employees')->insert([
                'name' => 'Employee 0'.$i,
                'designation_id' => ceil($i / 2),
            ]);
        }
    }
}
