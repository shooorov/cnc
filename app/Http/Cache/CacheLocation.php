<?php

namespace App\Http\Cache;

use App\Models\Location as ModelsLocation;
use Illuminate\Support\Facades\Cache;

class CacheLocation
{
    private static $key = 'location';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsLocation::orderBy('name')->get();
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
