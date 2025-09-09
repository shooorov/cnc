<?php

namespace App\Http\Cache;

use App\Models\Document as ModelsDocument;
use Illuminate\Support\Facades\Cache;

class CacheDocument
{
    private static $key = 'document';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsDocument::all();
        });
    }

    public static function find($value, $field_name = 'id')
    {
        return self::get()->first(fn ($i) => $value == $i->$field_name);
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
