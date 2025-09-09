<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Branch::updateOrCreate(['name' => 'Main Branch'], [
            'name' => 'Main Branch',
            'short_code' => 'main-branch',
            'phone' => '01704174843, 01704174844',
            'address' => '7, Rabindra Sarani, Uttara, Dhaka',
        ]);
    }
}
