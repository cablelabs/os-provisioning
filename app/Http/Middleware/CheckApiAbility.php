<?php

namespace App\Http\Middleware;

use Bouncer;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckApiAbility
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
        return (Auth::onceBasic() && Bouncer::can('use api')) ?: $next($request);
    }
}
