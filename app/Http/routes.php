<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// Home Route
Route::get('', array('as' => 'Home', 'uses' => 'WelcomeController@index'));


/*
 * Admin
 */

// Base Route, This will redirect depending on valid Login
Route::get('admin', array('as' => 'admin', 'uses' => 'AuthController@home'));

// Auth => login form
Route::get('admin/auth/login', array('as' => 'Auth.login', 'uses' => 'AuthController@showLoginForm'));

// Auth => process form data
Route::post('admin/auth/login', array('as' => 'Auth.login', 'uses' => 'AuthController@postLogin'));

// Auth => Logout
Route::get ('admin/auth/logout', array('as' => 'Auth.logout', 'uses' => 'AuthController@getLogout'));
Route::post('admin/auth/logout', array('as' => 'Auth.logout', 'uses' => 'AuthController@getLogout'));

// Auth Denied. For Error Handling
BaseRoute::get('admin/auth/denied', array('as' => 'Auth.denied', 'uses' => 'AuthController@denied'));

// Core Admin API
BaseRoute::group([], function() {

	// Base routes for global search
	BaseRoute::get('base/fulltextSearch', array('as' => 'Base.fulltextSearch', 'uses' => 'BaseController@fulltextSearch'));

	BaseRoute::resource('Authuser', 'AuthuserController');
	BaseRoute::resource('Authrole', 'AuthroleController');
	Route::post('Authuser/{id}/AssignRole', ['as' => 'AssignRole.add', 'uses' => 'AuthuserController@assign_roles']);
	Route::post('Authuser/DeleteRole', ['as' => 'AssignRole.delete', 'uses' => 'AuthuserController@delete_assigned_roles']);
	Route::post('Authrole/UpdateRight', ['as' => 'Right.update', 'uses' => 'AuthroleController@update_right']);

	BaseRoute::get('Config', array('as' => 'Config.index', 'uses' => 'GlobalConfigController@index'));
	BaseRoute::resource('GlobalConfig', 'GlobalConfigController');
	BaseRoute::resource('GuiLog', 'GuiLogController');

});