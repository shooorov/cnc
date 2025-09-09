<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RecipeImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
            new RecipeSheetImport(),
        ];
    }
}
