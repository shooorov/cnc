<?php

namespace App;

use App\Http\Cache\CacheKitchen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UseKitchen
{
    private static $key = 'kitchen';

    public static function set($kitchen_log)
    {
        Session::put(self::$key, $kitchen_log?->only(['id', 'started_at']));
    }

    public static function get()
    {
        return Session::get(self::$key);
    }

    public static function forget()
    {
        if (Session::has(self::$key)) {
            Session::forget(self::$key);
        }
    }

    public static function id()
    {
        return self::get() ? self::get()['id'] : null;
    }

    public static function startedAt()
    {
        return self::get() ? self::get()['started_at'] : null;
    }

    public static function all()
    {
        // return CacheKitchen::get()->filter(fn ($log) => $log->branch_id == UseBranch::id());
        return CacheKitchen::get()->where('branch_id', UseBranch::id())->when(! Auth::user()?->is_admin, function ($collection) {
            return $collection->where('user_id', Auth::user()?->id);
        });
    }
}
