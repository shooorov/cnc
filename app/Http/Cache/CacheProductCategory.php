<?php

namespace App\Http\Cache;

use App\Models\ProductCategory as ModelsProductCategory;
use Illuminate\Support\Facades\Cache;

class CacheProductCategory
{
    private static $key = 'product_category';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsProductCategory::orderBy('name')->get();
        });
    }

    public static function find($value, $field_name = 'id')
    {
        return self::get()->first(fn ($i) => $value == $i->$field_name);
    }

    public static function forget()
    {
        if (Cache::has(self::$key)) {
            Cache::forget(self::$key);
        }
    }
}
