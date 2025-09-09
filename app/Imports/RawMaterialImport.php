<?php

namespace App\Imports;

use App\Models\RawMaterial;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class RawMaterialImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            if ($key < 1) {
                continue;
            }

            $sl_no = filter_var($row[0], FILTER_SANITIZE_NUMBER_INT);
            $name = $row[1];
            // $name = filter_var($row[1], FILTER_SANITIZE_STRING);
            $category_name = filter_var($row[2], FILTER_SANITIZE_STRING);
            $unit = filter_var($row[3], FILTER_SANITIZE_STRING);
            $min_limit = filter_var($row[4], FILTER_SANITIZE_NUMBER_INT);
            $opening = filter_var($row[5], FILTER_SANITIZE_NUMBER_INT);

            // if(!RawMaterial::where('name', $name)->first()) {
            //     echo $sl_no . ' ' . $name . "\n";
            // }
            RawMaterial::updateOrCreate([
                'name' => $name,
            ], [
                'min_limit' => $min_limit,
                'opening' => $opening,
                'opening_date' => now()->subMonth()->endOfMonth(),
            ]);
        }
    }
}
