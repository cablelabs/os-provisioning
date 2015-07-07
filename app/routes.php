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

Route::resource('modem', 'ModemsController');
Route::post('modem/json', 'ModemsController@json');

Route::resource('endpoint', 'EndpointsController');
Route::post('endpoint/json', 'EndpointsController@json');

Route::resource('configfile', 'ConfigfilesController');