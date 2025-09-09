<?php

namespace App\Http\Cache;

use App\Models\OnlineProductCategory as ModelsOnlineProductCategory;
use Illuminate\Support\Facades\Cache;

class CacheOnlineProductCategory
{
    private static $key = 'online_product_category';

    public static function get()
    {
        return Cache::remember(self::$key, now()->addHours(2), function () {
            return ModelsOnlineProductCategory::orderBy('serial')->get();
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

    public static function all()
    {
        return self::get()
            ->filter(fn ($i) => $i->items->count() > 0)
            ->map(function ($category) {
                $category->products = $category
                    ->items
                    ->map(fn ($i) => $i->product)
                    ->values()
                    ->toArray();

                unset($category->items);

                return $category;
            })
            ->values();
    }
}
