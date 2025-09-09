<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Item::truncate();
        Schema::enableForeignKeyConstraints();

        // $file = database_path('seeders/data/items.csv');
        // if (($handle = fopen($file, 'r')) !== FALSE) {
        //     // necessary if a large csv file
        //     set_time_limit(0);

        //     $row = 0;

        //     while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
        //         // number of fields in the csv
        //         $col_count = count($data);
        //         $row++;
        //         if ($row == 1) {
        //             continue;
        //         }
        //         // dd($data);
        //         // get the values from the csv
        //         $category = ItemCategory::firstOrCreate([
        //             'name' => $data[2],
        //         ]);

        //         Item::create([
        //             'name'              => $data[0],
        //             'item_category_id'  => $category->id,
        //             'unit'              => $data[1],
        //         ]);
        //     }
        //     fclose($handle);
        // }

        $file = database_path('seeders/data/items2.csv');
        if (($handle = fopen($file, 'r')) !== false) {
            // necessary if a large csv file
            set_time_limit(0);

            $row = 0;

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // number of fields in the csv
                $col_count = count($data);
                $row++;

                $name = trim(filter_var($data[1], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
                $quantity = trim(filter_var($data[2], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
                $unit_short = trim(filter_var($data[3], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
                $price = trim(filter_var($data[4], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
                if ($row == 1 || empty($name)) {
                    continue;
                }

                $item = Item::where('name', $name)->first();

                if (! $item) {
                    echo 'Item not found => ' . $name . ' ' . $quantity . ' ' . $unit_short . "\n";
                } else {
                    echo 'Item found => '.$name."\n";
                }

                // dd($data);
                // get the values from the csv
                // $category = ItemCategory::firstOrCreate([
                //     'name' => $data[2],
                // ]);

                Item::updateOrCreate(['id' => $item?->id], [
                    'name' => $name,
                    'item_category_id' => 2,
                    'unit' => $unit_short ?? 'kg',
                    'avg_rate' => empty($price) ? 0 : ($price / $quantity),
                ]);
            }
            fclose($handle);
        }
    }
}
