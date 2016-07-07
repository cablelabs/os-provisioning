<?php namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's global HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		// L5 defaults
		'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Illuminate\Cookie\Middleware\EncryptCookies',
		'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
		'Illuminate\Session\Middleware\StartSession',
		'Illuminate\View\Middleware\ShareErrorsFromSession',
		'App\Http\Middleware\VerifyCsrfToken',
	];

	/**
	 * The application's route middleware.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		// Base Authentication Stuff
		// @author Torsten Schmidt
		'auth' => \App\Http\Middleware\BaseAuthMvcMiddleware::class,

		// TODO: check if absence of CCC module will break ?
		'ccc.base' => \Modules\Ccc\Http\Middleware\CccBaseMiddleware::class,

		// L5 defaults:
		//'auth' => 'App\Http\Middleware\Authenticate',
		//'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
		//'guest' => 'App\Http\Middleware\RedirectIfAuthenticated',
	];

}
