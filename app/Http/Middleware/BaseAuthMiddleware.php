<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\BaseAuthController;

class BaseAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if ($request->is('admin/*') && !$request->is('admin/auth/*'))
        //    return $next($request);

        return $next($request);
    }
}
