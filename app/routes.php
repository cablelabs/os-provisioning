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
