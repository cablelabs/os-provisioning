<?php namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's global HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
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
	    'auth.view' => \App\Http\Middleware\BaseAuthViewMiddleware::class,
	    'auth.create' => \App\Http\Middleware\BaseAuthCreateMiddleware::class,
	    'auth.edit' => \App\Http\Middleware\BaseAuthEditMiddleware::class,
	    'auth.delete' => \App\Http\Middleware\BaseAuthDeleteMiddleware::class,

		// L5 defaults:
		//'auth' => 'App\Http\Middleware\Authenticate',
		//'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
		//'guest' => 'App\Http\Middleware\RedirectIfAuthenticated',
	];

}
