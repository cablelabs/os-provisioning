<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\BaseAuthController;

class BaseAuthAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role=null)
    {
        BaseAuthController::auth_check($role);

        return $next($request);
    }
}
