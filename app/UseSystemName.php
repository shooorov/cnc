<?php

namespace App;

use App\Http\Cache\CacheSetting;
use Illuminate\Support\Str;

class UseSystemName
{
    public static function data()
    {
        $words = CacheSetting::get()->filter(function ($setting) {
            return $setting->type == 'rename';
        })->pluck('value', 'name')->toArray();

        return array_merge($words, []);
    }

    public static function get()
    {
        $string_change = [];
        foreach (self::data() as $item => $value) {
            $string_change[$item.'_s'] = Str::plural($value);
            $string_change[$item] = $value;
        }

        // dd($string_change);
        return $string_change;
    }

    public static function search()
    {
        return array_keys(self::data());
    }

    public static function replace()
    {
        return array_values(self::data());
    }
}
