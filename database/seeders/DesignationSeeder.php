<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Designation::truncate();
        Schema::enableForeignKeyConstraints();

        $designations = [
            'Manager',
            'Chef',
            'Waiter',
            'Barista',
            'Cleaner',
            'Commi 2',
            'Commi 1',
            'Cashier',
        ];

        foreach ($designations as $designation) {
            DB::table('designations')->insert([
                'name' => $designation,
            ]);
        }
    }
}
