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

// Home Route, This will redirect depending on valid Login
Route::get('', array('as' => 'Home', 'uses' => 'AuthController@home'));


// Auth => login form
Route::get('auth/login', array('as' => 'Auth.login', 'uses' => 'AuthController@showLogin'));

// Auth => process form data
Route::post('auth/login', array('as' => 'Auth.login', 'uses' => 'AuthController@doLogin'));

// Auth => Logout
Route::get ('auth/logout', array('as' => 'Auth.logout', 'uses' => 'AuthController@doLogout'));
Route::post('auth/logout', array('as' => 'Auth.logout', 'uses' => 'AuthController@doLogout'));

// Auth Denied. For Error Handling
Route::get('auth/denied', array('as' => 'Auth.denied', 'uses' => 'AuthController@denied'));


// Authentification is necessary before accessing a route
Route::group(array('before' => 'auth'), function() {
	// Base routes for global search
	Route::get('base/fulltextSearch', array('as' => 'Base.fulltextSearch', 'uses' => 'BaseController@fulltextSearch'));

	Route::resource('Authuser', 'AuthuserController');
	Route::get('Authuser/fulltextSearch', array('as' => 'Authuser.fulltextSearch', 'uses' => 'AuthuserController@fulltextSearch'));
});