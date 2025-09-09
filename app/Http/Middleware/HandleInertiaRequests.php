<?php

namespace App\Http\Middleware;

use App\Http\Cache\CacheSetting;
use App\Navigation;
use App\UseBranch;
use App\UseKitchen;
use App\UseSystemName;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $setting = CacheSetting::get()->pluck('value', 'name')->toArray();

        $logo = $setting['company_logo'] ?? '/logo.png';
        $logo_invoice = $setting['company_logo_invoice'] ?? '/logo-invoice.png';

        return [
            ...parent::share($request),
            'alertMessage' => [
                'fail' => fn () => $request->session()->get('fail'),
                'success' => fn () => $request->session()->get('success'),
                'successItems' => fn () => $request->session()->get('successItems'),
            ],
            'app' => [
                'base' => config('app.url'),
                'name' => config('app.name'),
                'logo' => file_exists(public_path($logo)) ? asset($logo) : '',
                'logo_invoice' => file_exists(public_path($logo_invoice)) ? asset($logo_invoice) : '',
            ],
            'auth' => [
                'user' => $request->user(),
            ],
            'branch' => UseBranch::get(),
            'branches' => UseBranch::available(),
            'kitchen' => UseKitchen::get(),
            'module' => config('module'),
            'navigation' => [
                'menu' => Navigation::get(UseBranch::id()),
                'routes' => Navigation::routes(),
            ],
            'setting' => $setting,
            'string_change' => UseSystemName::get(),
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
