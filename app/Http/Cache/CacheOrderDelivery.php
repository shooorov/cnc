<?php

namespace App\Http\Cache;

use App\Models\OrderDelivery as ModelsOrderDelivery;
use Illuminate\Support\Facades\Cache;

class CacheOrderDelivery
{
    private static $key = 'order_delivery';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsOrderDelivery::all();
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
