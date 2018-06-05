<?php

namespace Acme\core;

/**
 * BaseRoute API
 *
 * This Class will be used to create our own http routing functions
 *
 * @author Torsten Schmidt
 */
class BaseRoute {

	// HTML Admin Prefix for https://xyz/nmsprime/admin
	public static $admin_prefix = 'admin';


	/**
	 * Return the correct base URL
	 * @todo move somewhere else
	 * @return type string the actual base url
	 */
	public static function get_base_url()
	{
		$url = \Request::root();

		if (\Request::is('admin/*'))
			return $url.'/admin';

		if (\Request::is('customer/*'))
			return $url.'/customer';

		return $url; // will not work
	}


	/**
	 * Our own route resource function.
	 * This function takes care of authentication
	 *
	 * NOTE: we could not use parent resource function, because there is no way
	 *       to set individual middleware names! This will also have the benefit
	 *       of setting other stuff in feature more individually.
	 *
	 * TODO: maybe we should try to make this function more readable by out-sourcing
	 *       the large array() statements and preparing them in a separate function(?).
	 *
	 * @author Torsten Schmidt
	 *
	 * @param  string  $name
	 * @param  string  $controller
	 * @param  array  $options
	 * @return void
	 */
	public static function resource($name, $controller, array $options = [])
	{
		// Index
		\Route::get($name, array('as' => $name.'.index', 'uses' => $controller.'@index', $options, 'middleware' => 'auth:view'));
		\Route::get("api/v{ver}/$name", array('as' => $name.'.api_index', 'uses' => $controller.'@api_index', $options, 'middleware' => ['auth.basic', 'apiuser']));

		// Store
		\Route::post($name, array('as' => $name.'.store', 'uses' => $controller.'@store', $options, 'middleware' => 'auth:create'));
		\Route::post("api/v{ver}/$name", array('as' => $name.'.api_store', 'uses' => $controller.'@api_store', $options, 'middleware' => ['auth.basic', 'apiuser']));

		// Create
		\Route::get("$name/create", array('as' => $name.'.create', 'uses' => $controller.'@create', $options, 'middleware' => 'auth:create')); // for viewing
		\Route::post("$name/create", array('as' => $name.'.create', 'uses' => $controller.'@create', $options, 'middleware' => 'auth:create'));
		\Route::get("api/v{ver}/$name/create", array('as' => $name.'.api_create', 'uses' => $controller.'@api_create', $options, 'middleware' => ['auth.basic', 'apiuser']));
		\Route::post("api/v{ver}/$name/create", array('as' => $name.'.api_create', 'uses' => $controller.'@api_create', $options, 'middleware' => ['auth.basic', 'apiuser']));

		// update
		\Route::patch("$name/{{$name}}", array('as' => $name.'.update', 'uses' => $controller.'@update', $options, 'middleware' => 'auth:edit'));
		\Route::put("$name/{{$name}}", array('as' => $name.'.update', 'uses' => $controller.'@update', $options, 'middleware' => 'auth:edit'));
		\Route::patch("api/v{ver}/$name/{{$name}}", array('as' => $name.'.api_update', 'uses' => $controller.'@api_update', $options, 'middleware' => ['auth.basic', 'apiuser']));
		\Route::put("api/v{ver}/$name/{{$name}}", array('as' => $name.'.api_update', 'uses' => $controller.'@api_update', $options, 'middleware' => ['auth.basic', 'apiuser']));

		// delete
		\Route::delete("$name/{{$name}}", array('as' => $name.'.destroy', 'uses' => $controller.'@destroy', $options, 'middleware' => 'auth:delete'));
		\Route::delete("api/v{ver}/$name/{{$name}}", array('as' => $name.'.api_destroy', 'uses' => $controller.'@api_destroy', $options, 'middleware' => ['auth.basic', 'apiuser']));

		// edit
		\Route::get("$name/{{$name}}/edit", array('as' => $name.'.edit', 'uses' => $controller.'@edit', $options, 'middleware' => 'auth:view'));
		\Route::get("api/v{ver}/$name/{{$name}}", array('as' => $name.'.api_get', 'uses' => $controller.'@api_get', $options, 'middleware' => ['auth.basic', 'apiuser']));

		// Fulltext Search
		// TODO: adapt route name to not strtolower() like other functions
		\Route::get(strtolower($name).'/fulltextSearch', array('as' => $name.'.fulltextSearch', 'uses' => $controller.'@fulltextSearch', $options, 'middleware' => 'auth:view'));

		// AJAX Index DataTable
		\Route::get("$name/datatables", array('as' => $name.'.data', 'uses' => $controller.'@index_datatables_ajax', $options, 'middleware' => 'auth:view'));

		\Route::get("$name/select2/{column}", array ('as' => $name.'.select2', 'uses' => $controller.'@select2_ajax', $options, 'middleware' => 'auth:view'));

		// import
		\Route::get("$name/import", array('as' => $name.'.import', 'uses' => $controller.'@import', $options, 'middleware' => 'auth:create'));
		\Route::post("$name/import_parse", array('as' => $name.'.import_parse', 'uses' => $controller.'@import_parse', $options, 'middleware' => 'auth:create'));
		\Route::post("$name/import_process", array('as' => $name.'.import_process', 'uses' => $controller.'@import_process', $options, 'middleware' => 'auth:create'));
	}


	/**
	 * Our own route group with shared attributes and some default settings
	 * like prefix and as statement we MUST use.
	 *
	 * @author Torsten Schmidt
	 * @param  array  $attributes
	 * @param  \Closure  $callback
	 * @return void
	 */
	public static function group(array $attributes, \Closure $callback)
	{
		$attributes['prefix'] = self::$admin_prefix;
		$attributes['as'] = ''; // clear route name prefix

		// $attributes['before'] = 'auth'; // auth required ! -> deprecated !

		\Route::group($attributes, $callback);
	}


	/**
	 * The following functions are simple helpers to adapt automatic authentication stuff
	 */
	public static function get($uri, $action = null)
	{
		$action['middleware'] = 'auth:view';
		return \Route::get($uri, $action);
	}


	// requires edit permissions!!!
	public static function post($uri, $action = null)
	{
		$action['middleware'] = 'auth:edit';
		return \Route::post($uri, $action);
	}


	// requires edit permissions!!!
	public static function put($uri, $action = null)
	{
		$action['middleware'] = 'auth:edit';
		return \Route::put($uri, $action);
	}
}
