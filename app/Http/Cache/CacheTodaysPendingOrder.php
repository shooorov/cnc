<?php

namespace App\Http\Cache;

use App\Helpers;
use App\Models\Order as ModelsOrder;
use App\Models\Scopes\UseBranchScope;
use Illuminate\Support\Facades\Cache;

class CacheTodaysPendingOrder
{
    private static $key = 'pending_order';

    public static function get()
    {
        $end_date = Helpers::dayEndedAt();
        $start_date = Helpers::dayStartedAt();

        return Cache::remember(self::$key, now()->addMinutes(30), function () use ($start_date, $end_date) {

            return ModelsOrder::withoutGlobalScope(UseBranchScope::class)
                ->when($start_date, function ($query, $start_date) {
                    $query->where('date', '>=', $start_date);
                })
                ->when($end_date, function ($query, $end_date) {
                    $query->where('date', '<=', $end_date);
                })
                ->where('status', '!=', 'complete')
                ->orderBy('id', 'DESC')
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
