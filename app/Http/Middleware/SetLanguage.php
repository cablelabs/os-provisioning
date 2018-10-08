<?php

namespace App\Http\Middleware;

use App;
use Closure;
use App\Http\Controllers\BaseViewController;

class SetLanguage
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
        App::setLocale(BaseViewController::get_user_lang());

        return $next($request);
    }
}
