<?php

namespace App\Http\Middleware;

use App\Navigation;
use App\UseBranch;
use Closure;

class PreventUnauthorizedAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (($request->user()->status != 'active')) {
            abort(403, 'You are not active user!');
        }

        if (UseBranch::available()->count() == 0) {
            abort(403, 'You are not assigned to any branch!');
        }

        if ($request->user()->is_admin || Navigation::isRouteValid()) {
            return $next($request);
        }

        $current_route = $request->route()->getName();

        $waiter_routes = [
            'pos.create',
            'pos.store',
            'production.index',
            'production.delivered',
        ];

        if ($request->user()->is_waiter && in_array($current_route, $waiter_routes)) {
            return $next($request);
        }

        $cook_routes = [
            'kitchen.clock',
            'production.index',
            'production.view',
            'production.status',
        ];

        if ($request->user()->is_cook && in_array($current_route, $cook_routes)) {
            return $next($request);
        }

        $rider_routes = [
            'delivery.index',
            'delivery.load',
            'delivery.edit',
            'delivery.update',
            'delivery.show',
            'delivery.status.update',
        ];

        if ($request->user()->is_rider && in_array($current_route, $rider_routes)) {
            return $next($request);
        }

        abort(401);
    }
}
