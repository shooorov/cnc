<?php

namespace App\Http\Cache;

use App\Models\BranchAccess as ModelsBranchAccess;
use Illuminate\Support\Facades\Cache;

class CacheBranchAccess
{
    private static $key = 'branch_access';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsBranchAccess::all();
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
