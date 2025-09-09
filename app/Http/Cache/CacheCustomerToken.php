<?php

namespace App\Http\Cache;

use App\Models\CustomerToken as ModelsCustomerToken;
use Illuminate\Support\Facades\Cache;

class CacheCustomerToken
{
    private static $key = 'CustomerToken';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsCustomerToken::get();
        });
    }

    public static function whereIn($values, $field_name = 'customer_id')
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
