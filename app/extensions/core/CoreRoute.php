<?php

namespace Acme\core;

/**
 * CoreRoute API
 *
 * This Class will be used to create our own http routing functions
 */
class CoreRoute {

	// HTML Admin Prefix for https://xyz/lara/admin
	public static $admin_prefix = 'admin';


	/**
	 * Route a resource to a controller.
	 *
	 * @param  string  $name
	 * @param  string  $controller
	 * @param  array  $options
	 * @return void
	 */
	public static function resource($name, $controller, array $options = [])
	{
		// Fulltext Search
		\Route::get(strtolower($name).'/fulltextSearch', array('as' => $name.'.fulltextSearch', 'uses' => $controller.'@fulltextSearch'));

		// Second Create POST
		// This is required for relational creates to send pre-filled values with HTML POST
		\Route::post($name.'/create', array('as' => $name.'.create', 'uses' => $controller.'@create'));


		// Prepare resource command
		// set default routes
		if (!isset($options['only']))
			$options['only'] = ['index', 'create', 'store', 'edit', 'update', 'destroy'];

		// Remove route name prefix's, like "admin.Contract.create" to "Contract.create"
		// This should normally work with 'as' statement, but not in resource command
		// See Laravel bug report: https://github.com/laravel/framework/pull/4507
		$options['names'] = ['index' => $name.'.index',
							'create' => $name.'.create',
							'store' => $name.'.store',
							'edit' => $name.'.edit',
							'update' => $name.'.update',
							'destroy' => $name.'.destroy',
							'show' => $name.'.show'];

		// resource command
		\Route::resource($name, $controller, $options);
	}


	/**
	 * Create a route group with shared attributes.
	 *
	 * @param  array  $attributes
	 * @param  \Closure  $callback
	 * @return void
	 */
	public static function group(array $attributes, \Closure $callback)
	{
		$attributes['prefix'] = self::$admin_prefix;
		$attributes['before'] = 'auth'; // auth required !
		$attributes['as'] = ''; // clear route name prefix

		\Route::group($attributes, $callback);
	}
}