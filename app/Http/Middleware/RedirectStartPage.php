<?php

namespace App\Http\Middleware;

use Closure;

class RedirectStartPage
{
    /**
     * Start Page: Page which show link to admin or ccc panel
     * This site is only required if Admin and CCC runs on same port
     *
     * USE: HTTPS_ADMIN_PORT or
     *      HTTPS_CCC_PORT
     *
     *      in .env file to skip start page and go directly to admin or ccc
     *
     * NOTE: if different ports are used, take care that apache conf.d files
     *       are used in correct manner! See Documentation / Confluence..
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // default settings
        // NOTE: required for backward compatibility
        //       admin and ccc run's on same port
        $admin_port = env('HTTPS_ADMIN_PORT', '443');
        $ccc_port   = env('HTTPS_CCC_PORT', '443');

        // if same port, show start page
        if ($admin_port == $ccc_port)
            return $next($request);

		if (env('APP_ENV') == 'testing') {
			// $_SERVER['SERVER_PORT'] does not exist if running phpunit
			$server_port = \Request::getPort();
		}
		else {
			$server_port = $_SERVER['SERVER_PORT'];
		}

        if ($server_port == $admin_port)
            return redirect('admin');

        if ($server_port == $ccc_port)
            return redirect('customer');

        // start page
        return $next($request);
    }
}
