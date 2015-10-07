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
Route::get('', 'ModemsController@index');

// Modem
Route::get('modem/{modem}/ping', array ('as' => 'modem.ping', 'uses' => 'ModemsController@ping'));
Route::get('modem/{modem}/monitoring', array ('as' => 'modem.monitoring', 'uses' => 'ModemsController@monitoring'));
Route::get('modem/{modem}/log', array ('as' => 'modem.log', 'uses' => 'ModemsController@log'));
Route::get('modem/{modem}/lease', array ('as' => 'modem.lease', 'uses' => 'ModemsController@lease'));
Route::post('modem/json', 'ModemsController@json');
Route::resource('modem', 'ModemsController');


Route::resource('endpoint', 'EndpointsController');
Route::post('endpoint/json', 'EndpointsController@json');

Route::resource('configfile', 'ConfigfilesController');

Route::resource('quality', 'QualitiesController');

Route::resource('mta', 'MtasController');
