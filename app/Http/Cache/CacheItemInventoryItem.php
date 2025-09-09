<?php

namespace App\Http\Cache;

use App\Models\ItemInventoryItem as ModelsItemInventoryItem;
use Illuminate\Support\Facades\Cache;

class CacheItemInventoryItem
{
    private static $key = 'item_inventory_item';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsItemInventoryItem::all();
        });
    }

    public static function whereIn($values, $field_name = 'id')
    {
        return self::get()->whereIn($field_name, $values);
    }

    public static function forget()
    {
        if (Cache::has(self::$key)) {
            Cache::forget(self::$key);
        }
    }
}
