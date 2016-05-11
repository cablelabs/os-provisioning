<?php

namespace Acme\core;

/**
 * CoreRoute API
 *
 * This Class will be used to create our own http routing functions
 */
class CoreRoute {

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
		\Route::get(strtolower($name).'/fulltextSearch', array('as' => $name.'.fulltextSearch', 'uses' => $controller.'@fulltextSearch'));
		\Route::post($name.'/create', array('as' => $name.'.create', 'uses' => $controller.'@create'));
		\Route::resource($name, $controller, ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
	}

}