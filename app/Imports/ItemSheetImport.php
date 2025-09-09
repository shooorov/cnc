<?php

namespace App\Imports;

use App\Models\Item;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ItemSheetImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $name = '';
        $item = null;

        foreach ($rows as $key => $row) {
            $sl = trim(filter_var($row[0], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $name = trim(filter_var($row[1], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $quantity = trim(filter_var($row[2], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $quantity2 = trim(filter_var($row[3], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $price = trim(filter_var($row[4], FILTER_SANITIZE_FULL_SPECIAL_CHARS));

            // echo "Name => " . $name . "\n";

            if (empty($name) && $key == 0) {
                // echo "row skip" . "\n";
                continue;
            }

            if ($sl == 'No') {
                continue;
            }
            $name = trim(filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $item = Item::where('name', $name)->first();

            if (! $item) {
                echo 'Item not found => '.$name."\n";
            } else {
                echo 'Item found => '.$name."\n";
            }

            // $item = Item::where('name', $name)->first();
            // $unit = Unit::where('name', $unit_short)->first();

            // dd(
            //     $quantity,
            //     $item,
            //     $item,
            // );

            // echo $item_name . " Item Item\n";
            // DB::table('item_items')->insert([
            //     'quantity'      => $quantity,
            //     'item_id'       => $item->id,
            //     'item_id'    => $item->id,
            // ]);
        }
    }
}
