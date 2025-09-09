<?php

namespace App\Http\Cache;

use App\Models\OrderProduct as ModelsOrderProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheOrderProductQuantity
{
    private static $key = 'order_product_quantity';

    public static function get($start_date = null, $end_date = null)
    {
        $key = [
            $start_date,
            self::$key,
            $end_date,
        ];

        $key = implode(' - ', $key);

        return Cache::remember($key, now()->addHours(2), function () use ($start_date, $end_date) {

            return ModelsOrderProduct::when($start_date, function ($query, $start_date) {
                $query->where('created_at', '>=', $start_date);
            })
                ->when($end_date, function ($query, $end_date) {
                    $query->where('created_at', '<=', $end_date);
                })
                ->select('product_id', DB::raw('SUM(quantity) as product_quantity'))
                ->groupBy('product_id')
                ->orderBy('product_quantity')
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
