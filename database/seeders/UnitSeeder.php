<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('units')->insert([
            'name' => 'KiloGram',
            'short' => 'Kg',
        ]);
        DB::table('units')->insert([
            'name' => 'Liter',
            'short' => 'L',
        ]);
        DB::table('units')->insert([
            'name' => 'Piece',
            'short' => 'pc',
        ]);
        DB::table('units')->insert([
            'name' => 'Gram',
            'short' => 'g',
        ]);
        DB::table('units')->insert([
            'name' => 'MilliLiter',
            'short' => 'ml',
        ]);

        DB::table('unit_conversions')->insert([
            'to_unit_id' => 1,
            'from_unit_id' => 4,
            'factor' => 1 / 1000,
        ]);

        DB::table('unit_conversions')->insert([
            'to_unit_id' => 4,
            'from_unit_id' => 1,
            'factor' => 1000,
        ]);

        DB::table('unit_conversions')->insert([
            'to_unit_id' => 2,
            'from_unit_id' => 5,
            'factor' => 1 / 1000,
        ]);

        DB::table('unit_conversions')->insert([
            'to_unit_id' => 5,
            'from_unit_id' => 2,
            'factor' => 1000,
        ]);
    }
}
