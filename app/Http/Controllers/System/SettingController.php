<?php

namespace App\Http\Controllers\System;

use App\Http\Cache\CacheSetting;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application settings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit()
    {
        $hidden_types = Setting::$hidden_types;

        $settings = CacheSetting::get()->filter(function ($setting) use ($hidden_types) {
            return ! in_array($setting->type, $hidden_types);
        })->values();

        $admin_settings = CacheSetting::get()->filter(function ($setting) use ($hidden_types) {
            return in_array($setting->type, $hidden_types);
        })->values();

        $params = [
            'settings' => $settings,
            'admin_settings' => Auth::user()->is_admin ? $admin_settings : [],
        ];

        return Inertia::render('System/Setting', $params);
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        foreach ($request->settings as $item) {
            $setting = Setting::find($item['id']);

            if ($setting->value != $item['value']) {
                $setting->update(['value' => $item['value'] ?? '']);
            }
        }

        foreach ($request->admin_settings as $item) {
            $setting = Setting::find($item['id']);

            if ($setting->value != $item['value']) {
                $setting->update(['value' => $item['value'] ?? '']);
            }
        }

        DB::commit();
        CacheSetting::forget();

        return back()->with('success', 'Settings updated successfully');
    }
}
