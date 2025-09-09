<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class RecipeSheetImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $name = '';
        $product = null;
        $recipe_quantity = null;

        foreach ($rows as $key => $row) {
            $sl = trim(filter_var($row[0], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $name = trim(filter_var($row[1], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $quantity = trim(filter_var($row[2], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $unit_short = trim(filter_var($row[3], FILTER_SANITIZE_FULL_SPECIAL_CHARS));

            // echo "Name => " . $name . "\n";

            if (empty($sl) && empty($quantity) && empty($unit_short) && empty($name)) {
                // echo "row skip" . "\n";
                continue;
            }

            if ($sl == 'Menu') {
                $name = trim(filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
                $product = Product::where('name', $name)->first();
                $recipe_quantity = $quantity;
                if (! $product) {
                    echo 'Product not found => '.$name.' - '.$quantity."\n";
                } else {
                    // echo "Product found => " . $name . " - " . $quantity . "\n";
                }

                continue;
            } else {
                $name = trim(filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
                $item = Item::where('name', $name)->first();

                if ($name == 'নাম' || empty($quantity)) {
                    continue;
                }

                if (! $item) {
                    echo $name."\n";

                    // echo $product?->name . " Item not found => " . $name . " - " . $quantity . "\n";
                    continue;
                } else {
                    // echo $product?->name . " Item found => " . $name . " - " . $quantity . "\n";
                }
            }

            $item = Item::where('name', $name)->first();
            $unit = Unit::where('name', $unit_short)->first();
            if ($unit_short == 'g' || $unit_short == 'ml') {
                $quantity = $quantity / 1000;
            }

            // echo $name . " Recipe Item\n";
            DB::table('product_items')->insert([
                'quantity' => $quantity / $recipe_quantity,
                'item_id' => $item->id,
                'product_id' => $product?->id,
            ]);
        }
    }
}
