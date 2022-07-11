<?php

namespace App\Http\Middleware;

use Closure;

class RouteModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $module)
    {
        \Illuminate\Support\Facades\View::share('routeModule', $module);

        return $next($request);
    }
}
