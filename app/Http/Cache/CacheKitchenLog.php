<?php

namespace App\Http\Cache;

use App\Models\KitchenLog as ModelsKitchenLog;
use Illuminate\Support\Facades\Cache;

class CacheKitchenLog
{
    private static $key = 'kitchen_log';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsKitchenLog::withoutGlobalScope(UseBranchScope::class)->orderBy('started_at')->get();
        });
    }

    public static function forget()
    {
        if (Cache::has(self::$key)) {
            Cache::forget(self::$key);
        }
    }
}
