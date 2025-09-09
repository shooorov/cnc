<?php

namespace App\Http\Cache;

use App\Models\Event as ModelsEvent;
use App\UseBranch;
use Illuminate\Support\Facades\Cache;

class CacheEvent
{
    private static $key = 'event';

    private static function cacheKey()
    {
        $branch_id = UseBranch::id() ?? 'all';

        return self::$key.'_'.$branch_id;
    }

    public static function get()
    {
        $cache_key = self::cacheKey();

        return Cache::remember($cache_key, now()->addHours(2), function () {
            return ModelsEvent::orderBy('name')->get();
        });
    }

    public static function forget()
    {
        $cache_key = self::cacheKey();

        if (Cache::has($cache_key)) {
            Cache::forget($cache_key);
        }
    }
}
