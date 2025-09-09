<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Product::truncate();
        Schema::enableForeignKeyConstraints();

        $file = database_path('seeders/data/products.csv');
        if (($handle = fopen($file, 'r')) !== false) {
            // necessary if a large csv file
            set_time_limit(0);

            $row = 0;
            $category = null;

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // number of fields in the csv
                $col_count = count($data);
                $row++;

                if (empty($data[0])) {
                    continue;
                }

                if (strtolower($data[1]) == 'price') {
                    $category = ProductCategory::firstOrCreate([
                        'name' => $data[0],
                    ]);

                    continue;
                }

                if ($category) {
                    $code = Product::count() + 1;

                    Product::create([
                        'code' => $code,
                        'name' => $data[0],
                        'rate' => $data[1],
                        'discount' => 0,
                        'product_category_id' => $category->id,
                    ]);
                }
            }
            fclose($handle);
        }
    }
}
