<?php

namespace Acme\core;

/**
 * BaseRoute API
 *
 * This Class will be used to create our own http routing functions
 */
class BaseRoute {

	// HTML Admin Prefix for https://xyz/lara/admin
	public static $admin_prefix = 'admin';


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
		\Route::get($name, array('as' => $name.'.index', 'uses' => $controller.'@index', $options, 'middleware' => 'auth.view'));

		// Store
		\Route::post($name, array('as' => $name.'.store', 'uses' => $controller.'@store', $options, 'middleware' => 'auth.create'));

		// Create
		\Route::get($name.'/create', array('as' => $name.'.create', 'uses' => $controller.'@create', $options, 'middleware' => 'auth.create')); // for viewing
		\Route::post($name.'/create', array('as' => $name.'.create', 'uses' => $controller.'@create', $options, 'middleware' => 'auth.create'));

		// update
		\Route::patch("$name/{{$name}}", array('as' => $name.'.update', 'uses' => $controller.'@update', $options, 'middleware' => 'auth.edit'));
		\Route::put("$name/{{$name}}", array('as' => $name.'.update', 'uses' => $controller.'@update', $options, 'middleware' => 'auth.edit'));

		// delete
		\Route::delete("$name/{{$name}}", array('as' => $name.'.destroy', 'uses' => $controller.'@destroy', $options, 'middleware' => 'auth.delete'));

		// edit
		\Route::get("$name/{{$name}}/edit", array('as' => $name.'.edit', 'uses' => $controller.'@edit', $options, 'middleware' => 'auth.edit'));

		// Fulltext Search
		// TODO: adapt route name to not strtolower() like other functions
		\Route::get(strtolower($name).'/fulltextSearch', array('as' => $name.'.fulltextSearch', 'uses' => $controller.'@fulltextSearch', $options, 'middleware' => 'auth.view'));
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
}