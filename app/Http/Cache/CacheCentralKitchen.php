<?php

namespace App\Http\Cache;

use App\Models\CentralKitchen as ModelsCentralKitchen;
use Illuminate\Support\Facades\Cache;

class CacheCentralKitchen
{
    private static $key = 'CentralKitchen';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsCentralKitchen::orderBy('name')->get();
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
