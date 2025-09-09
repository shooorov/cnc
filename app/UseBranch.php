<?php

namespace App;

use App\Http\Cache\CacheBranch;
use App\Http\Cache\CacheBranchAccess;
use Illuminate\Support\Facades\Auth;

class UseBranch
{

    public static function set($branch)
    {
        session()->put('branch', $branch?->id);
    }

    public static function get($branchId = null)
    {
		if(!self::id() && self::available()->count() == 1) {
			self::set(self::available()->first());
		}
		
		$id = $branchId ?? self::id();

        return self::available()->first(fn ($branch) => $branch->id == $id);
    }

    public static function id()
    {
        return session()->get('branch');
    }

    public static function take($field)
    {
        return self::get()?->$field;
    }

    public static function available()
    {
        $user = Auth::user();
        $branches = CacheBranch::get();

        if ($user?->is_admin) {
            return $branches;
        }

        $valid_ids = CacheBranchAccess::get()->filter(function ($branch_access) use ($user) {
            return $branch_access->user_id == $user?->id && $branch_access->is_checked;
        })->pluck('branch_id')->toArray();

        return $branches->filter(function ($branch) use ($valid_ids) {
            return in_array($branch->id, $valid_ids);
        })->values();
    }
}
