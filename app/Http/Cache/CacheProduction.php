<?php

namespace App\Http\Cache;

use App\Helpers;
use App\Models\Production as ModelsProduction;
use App\UseBranch;
use Illuminate\Support\Facades\Cache;

class CacheProduction
{
    private static $key = 'production';

    private static function cacheKey($date = null)
    {
        $key = self::$key;
        $branch_id = UseBranch::id();
        $day = now()->parse($date)->format('d');
        $date = ! $date ? now() : $date;

        return $key.'_'.$branch_id.'_'.$day;
    }

    public static function get($date = null)
    {
        $end_date = Helpers::dayEndedAt($date);
        $start_date = Helpers::dayStartedAt($date);

        $cache_key = self::cacheKey($date);

        return Cache::remember($cache_key, now()->addHours(2), function () use ($start_date, $end_date) {
            return ModelsProduction::when($start_date, function ($query, $start_date) {
                $query->where('created_at', '>=', $start_date);
            })
                ->when($end_date, function ($query, $end_date) {
                    $query->where('created_at', '<=', $end_date);
                })
                ->orderBy('id', 'ASC')
                ->get()
                ->filter(fn ($p) => $p->order?->status != 'complete' && $p->status != 'complete');
        });
    }

    public static function all()
    {
        $cache_key = self::cacheKey().'_all';

        return Cache::remember($cache_key, now()->addMinutes(10), function () {
            return ModelsProduction::all();
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
