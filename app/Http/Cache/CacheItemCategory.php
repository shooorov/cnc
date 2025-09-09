<?php

namespace App\Http\Cache;

use App\Models\ItemCategory as ModelsItemCategory;
use Illuminate\Support\Facades\Cache;

class CacheItemCategory
{
    private static $key = 'item_category';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsItemCategory::orderBy('name')->get();
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
