<?php

namespace App\Http\Cache;

use App\Models\EmailDigest as ModelsEmailDigest;
use Illuminate\Support\Facades\Cache;

class CacheEmailDigest
{
    private static $key = 'email_digest';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsEmailDigest::all();
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
