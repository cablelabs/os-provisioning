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
Route::get('', array('as' => 'Home', 'uses' => 'WelcomeController@index', 'middleware' => ['web']));

/*
 * Admin Login Routes
 */
Route::group(['prefix' => 'admin', 'middleware' => ['web']], function() {
	Route::get('login',	[
		'as' => 'adminLogin',
		'uses' => 'Auth\LoginController@showLoginForm',
	]);

	Route::post('login', [
		'as' => 'login.post',
		'uses' => 'Auth\LoginController@login',
	]);

	Route::post('logout', [
		'as' => 'logout.post',
		'uses' => 'Auth\LoginController@logout',
	]);
});

// Core Admin API
BaseRoute::group([], function() {

	BaseRoute::resource('GlobalConfig', 'GlobalConfigController');
	BaseRoute::resource('GuiLog', 'GuiLogController');
	BaseRoute::resource('User', 'Auth\UserController');
	BaseRoute::resource('Role', 'Auth\RoleController');

	BaseRoute::get('base/fulltextSearch', [
		'as' => 'Base.fulltextSearch',
		'uses' => 'BaseController@fulltextSearch',
	]);

	BaseRoute::get('Config', [
		'as' => 'Config.index',
		'uses' => 'GlobalConfigController@index',
	]);

	BaseRoute::get('Guilog/FilterRecords', [
		'as' => 'GuiLog.filter',
		'uses' => 'GuiLogController@filter',
		'middleware' => ["can:view,App\GuiLog"],
	]);

	BaseRoute::post('user/detach/{id}/{func}', [
		'as' => 'user.detach',
		'uses' => 'Auth\UserController@detach',
		'middleware' => ["can:delete,App\User"],
	]);

	BaseRoute::post('role/customAbility', [
		'as' => 'customAbility.update',
		'uses' => 'Auth\RoleController@assignCustomAbility',
		'middleware' => ["can:update,App\Role"],
	]);

	BaseRoute::post('role/Ability', [
		'as' => 'Ability.update',
		'uses' => 'Auth\RoleController@assignModelAbility',
		'middleware' => ["can:update,App\Role"],
	]);

});
