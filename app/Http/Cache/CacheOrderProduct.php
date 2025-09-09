<?php

namespace App\Http\Cache;

use App\Models\OrderProduct as ModelsOrderProduct;
use Illuminate\Support\Facades\Cache;

class CacheOrderProduct
{
    private static $key = 'order_product';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsOrderProduct::all();
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
