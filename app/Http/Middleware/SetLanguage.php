<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Session;
use Modules\Ccc\Entities\Ccc;
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
        if ($request->is('customer*')) {
            App::setLocale(Session::get('ccc-language') ?: checkLocale(Ccc::first()->language));

            return $next($request);
        }

        App::setLocale(BaseViewController::get_user_lang());

        return $next($request);
    }
}
