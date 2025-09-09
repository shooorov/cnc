<?php

namespace App\Http\Cache;

use App\Models\Branch as ModelsBranch;
use Illuminate\Support\Facades\Cache;

class CacheBranch
{
    private static $key = 'branch';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsBranch::where('status', 'active')->orderBy('name')->get();
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
