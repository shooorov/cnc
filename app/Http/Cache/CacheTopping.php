<?php

namespace App\Http\Cache;

use App\Models\Topping as ModelsTopping;
use Illuminate\Support\Facades\Cache;

class CacheTopping
{
    private static $key = 'topping';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsTopping::orderBy('name')->get();
        });
    }

    public static function forget()
    {
        if (Cache::has(self::$key)) {
            Cache::forget(self::$key);
        }
    }
}
