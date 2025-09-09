<?php

namespace App\Http\Cache;

use App\Helpers;
use App\Models\KitchenLog as ModelsKitchenLog;
use Illuminate\Support\Facades\Cache;

class CacheKitchen
{
    private static $key = 'kitchen';

    public static function get()
    {
        $end_date = Helpers::dayEndedAt();
        $start_date = Helpers::dayStartedAt();

        return Cache::remember(self::$key, now()->addHours(2), function () use ($end_date, $start_date) {
            return ModelsKitchenLog::withoutGlobalScope(UseBranchScope::class)
                ->whereNotNull('started_at')
                ->whereNull('ended_at')
                ->where('started_at', '>=', $start_date)
                ->where('started_at', '<=', $end_date)
                ->get();
        });
    }

    public static function forget()
    {
        if (Cache::has(self::$key)) {
            Cache::forget(self::$key);
        }
    }
}
