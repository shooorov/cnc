<?php

namespace App\Http\Cache;

use App\Models\Role as ModelsRole;
use Illuminate\Support\Facades\Cache;

class CacheRole
{
    private static $key = 'role';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsRole::orderBy('name')->get();
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
