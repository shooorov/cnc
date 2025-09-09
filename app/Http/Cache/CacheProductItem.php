<?php

namespace App\Http\Cache;

use App\Models\ProductItem as ModelsProductItem;
use Illuminate\Support\Facades\Cache;

class CacheProductItem
{
    private static $key = 'product_item';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsProductItem::all();
        });
    }

    public static function forget()
    {
        if (Cache::has(self::$key)) {
            Cache::forget(self::$key);
        }
    }
}
