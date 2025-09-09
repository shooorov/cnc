<?php

namespace Database\Seeders;

use App\Imports\RecipeImport;
use App\Models\ProductItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class RecipeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        ProductItem::truncate();
        Schema::enableForeignKeyConstraints();

        ini_set('memory_limit', '1024M');

        $file = database_path('seeders/data/recipe3.xlsx');
        Excel::import(new RecipeImport, $file);

        // ini_set('memory_limit', '64M');
    }
}
