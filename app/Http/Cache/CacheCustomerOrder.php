<?php

namespace App\Http\Cache;

use App\Models\Order;
use App\Models\Scopes\UseBranchScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheCustomerOrder
{
    private static $key = 'customer_order';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return Order::withoutGlobalScope(UseBranchScope::class)->select('branch_id', 'customer_id', DB::raw('COUNT(*) as order_quantity'))
                ->whereNotNull('customer_id')
                ->whereNotNull('branch_id')
                ->groupBy('branch_id', 'customer_id')
                ->get();
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
