<?php

namespace Database\Seeders;

use App\Models\OnlineProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OnlineProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        OnlineProductCategory::truncate();
        Schema::enableForeignKeyConstraints();

        $names = [
            'Category 0',
            'Category 0',
            'Category 0',
            'Category 0',
            'Category 0',
            'Category 0',
        ];

        foreach ($names as $serial => $name) {
            DB::table('online_product_categories')->insert([
                'name' => Str::of($name)->title().++$serial,
                'serial' => $serial,
            ]);
        }

        Artisan::call('optimize:clear');
    }
}
