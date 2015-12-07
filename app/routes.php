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

// Auth => login form
Route::get('login', array('uses' => 'AuthController@showLogin'));

// Auth => process form data
Route::post('login', array('uses' => 'AuthController@doLogin'));


// Authentification is necessary before accessing a route
Route::group(array('before' => 'auth'), function() {
	// Base routes for global search
	Route::get('base/fulltextSearch', array('as' => 'Base.fulltextSearch', 'uses' => 'BaseController@fulltextSearch'));
});