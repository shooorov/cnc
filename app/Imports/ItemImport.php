<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ItemImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            null,
            new ItemSheetImport(),
        ];
    }
}
