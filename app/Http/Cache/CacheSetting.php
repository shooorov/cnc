<?php

namespace App\Http\Cache;

use App\Models\Setting as ModelsSetting;
use Illuminate\Support\Facades\Cache;

class CacheSetting
{
    private static $key = 'setting';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsSetting::all();
        });
    }

    public static function forget()
    {
        if (Cache::has(self::$key)) {
            Cache::forget(self::$key);
        }
    }
}
