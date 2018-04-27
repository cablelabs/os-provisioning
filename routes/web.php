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
 * Admin Login Routes
 */
Route::group(['prefix' => 'admin', 'middleware' => ['web']], function() {
	Route::get('login', array('as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm'));
	Route::post('login', array('as' => 'login.post', 'uses' => 'Auth\LoginController@login'));
	Route::post('logout', array('as' => 'logout.post', 'uses' => 'Auth\LoginController@logout'));
});

// Core Admin API
BaseRoute::group([], function() {

	// Base routes for global search
	BaseRoute::get('base/fulltextSearch', array('as' => 'Base.fulltextSearch', 'uses' => 'BaseController@fulltextSearch'));

	BaseRoute::resource('User', 'Auth\UserController');
	BaseRoute::resource('Role', 'Auth\RoleController');
	BaseRoute::post('user/detach/{id}/{func}', ['as' => 'user.detach', 'uses' => 'Auth\UserController@detach']);
	BaseRoute::post('role/UpdatePermission', ['as' => 'Permission.update', 'uses' => 'Auth\RoleController@update_permission']);
	BaseRoute::post('role/AssignPermission', ['as' => 'Permission.assign', 'uses' => 'Auth\RoleController@assign_permission']);
	BaseRoute::post('role/DeletePermission', ['as' => 'Permission.delete', 'uses' => 'Auth\RoleController@delete_permission']);

	BaseRoute::get('Config', array('as' => 'Config.index', 'uses' => 'GlobalConfigController@index'));
	BaseRoute::resource('GlobalConfig', 'GlobalConfigController');
	BaseRoute::resource('GuiLog', 'GuiLogController');
	BaseRoute::get('Guilog/FilterRecords', ['as' => 'GuiLog.filter', 'uses' => 'GuiLogController@filter']);

});
