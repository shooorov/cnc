<?php

namespace App\Http\Cache;

use App\Models\PaymentMethod as ModelsPaymentMethod;
use Illuminate\Support\Facades\Cache;

class CachePaymentMethod
{
    private static $key = 'payment_method';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsPaymentMethod::orderBy('name')->get();
        });
    }

    public static function forget()
    {
        if (Cache::has(self::$key)) {
            Cache::forget(self::$key);
        }
    }
}
