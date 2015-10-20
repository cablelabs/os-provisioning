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
Route::get('', 'ModemController@index');

// Modem
Route::get('modem/{modem}/ping', array ('as' => 'modem.ping', 'uses' => 'ModemController@ping'));
Route::get('modem/{modem}/monitoring', array ('as' => 'modem.monitoring', 'uses' => 'ModemController@monitoring'));
// array (name, function of Controller)
Route::get('modem/{modem}/log', array ('as' => 'modem.log', 'uses' => 'ModemController@log'));
Route::get('modem/{modem}/lease', array ('as' => 'modem.lease', 'uses' => 'ModemController@lease'));
Route::post('modem/json', 'ModemController@json');

// routes controller with predefined methods
// add array('only' => array('edit', 'update')) as third parameter to only allow these routes
Route::resource('modem', 'ModemController');
Route::resource('cmts', 'CmtsController');  
Route::resource('ipPool', 'IpPoolController');  


Route::resource('endpoint', 'EndpointController');
Route::post('endpoint/json', 'EndpointController@json');

Route::resource('configfile', 'ConfigfileController');

Route::resource('quality', 'QosController');

Route::resource('mta', 'MtaController');
