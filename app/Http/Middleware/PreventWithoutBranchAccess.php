<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use App\UseBranch;
use Closure;

class PreventWithoutBranchAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if(! UseBranch::id()) {
        	return redirect()->intended(RouteServiceProvider::HOME)->with('fail', 'Please select branch to proceed');
		}

		return $next($request);
    }
}
