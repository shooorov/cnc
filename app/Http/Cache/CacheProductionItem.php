<?php

namespace App\Http\Cache;

use App\Models\ProductionItem as ModelsProductionItem;
use Illuminate\Support\Facades\Cache;

class CacheProductionItem
{
    private static $key = 'production_item';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsProductionItem::all();
        });
    }

    public static function forget()
    {
        if (Cache::has(self::$key)) {
            Cache::forget(self::$key);
        }
    }
}
