<?php

namespace App\Http\Middleware;

use Closure;

class RedirectStartPage
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
        if (str_contains($request->url(), ':8080/'))
            return redirect('admin');
        else
            return redirect('customer');

        return $next($request);
    }
}
