<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\BaseAuthController;

class BaseAuthEditMiddleware
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
        BaseAuthController::auth_check('edit');

        return $next($request);
    }
}
