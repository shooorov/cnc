<?php

namespace App\Http\Middleware;

use App\Models\Order;
use App\UseBranch;
use Closure;
use Illuminate\Http\Request;

class EnsureBranchSpecific
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty(UseBranch::id())) {
            $routes = [
                'pos.create',
                'pos.print',
            ];

            $order = Order::find($request->order_id);

            if (in_array($request->route()->getName(), $routes) && $order?->branch) {
                UseBranch::set($order?->branch);
            }
        }

        return $next($request);
    }
}
