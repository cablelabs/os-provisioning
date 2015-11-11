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
Route::get('modem/{modem}/ping', array ('as' => 'Modem.ping', 'uses' => 'ModemController@ping'));
Route::get('modem/{modem}/monitoring', array ('as' => 'Modem.monitoring', 'uses' => 'ModemController@monitoring'));
// array (name, function of Controller)
Route::get('modem/{modem}/log', array ('as' => 'Modem.log', 'uses' => 'ModemController@log'));
Route::get('modem/{modem}/lease', array ('as' => 'Modem.lease', 'uses' => 'ModemController@lease'));
Route::post('modem/json', 'ModemController@json');

// Searches
Route::get('modem/fulltextSearch', array('as' => 'Modem.fulltextSearch', 'uses' => 'ModemController@fulltextSearch'));
Route::get('cmts/fulltextSearch', array('as' => 'Cmts.fulltextSearch', 'uses' => 'CmtsController@fulltextSearch'));
Route::get('ippool/fulltextSearch', array('as' => 'IpPool.fulltextSearch', 'uses' => 'IpPoolController@fulltextSearch'));
Route::get('endpoint/fulltextSearch', array('as' => 'Endpoint.fulltextSearch', 'uses' => 'EndpointController@fulltextSearch'));
Route::get('configfile/fulltextSearch', array('as' => 'Configfile.fulltextSearch', 'uses' => 'ConfigfileController@fulltextSearch'));
Route::get('qos/fulltextSearch', array('as' => 'Qos.fulltextSearch', 'uses' => 'QosController@fulltextSearch'));
Route::get('phonenumber/fulltextSearch', array('as' => 'Phonenumber.fulltextSearch', 'uses' => 'PhonenumberController@fulltextSearch'));
Route::get('mta/fulltextSearch', array('as' => 'Mta.fulltextSearch', 'uses' => 'MtaController@fulltextSearch'));

// routes controller with predefined methods
// add array('only' => array('edit', 'update')) as third parameter to only allow these routes

Route::resource('Modem', 'ModemController');
Route::resource('Cmts', 'CmtsController');  
Route::resource('IpPool', 'IpPoolController');  
Route::resource('Endpoint', 'EndpointController');
#Route::post('endpoint/json', 'EndpointController@json');
Route::resource('Configfile', 'ConfigfileController');
Route::resource('Qos', 'QosController');

Route::resource('SnmpMib', 'SnmpMibController');
Route::resource('SnmpValue', 'SnmpValueController');
Route::resource('CmtsDownstream', 'CmtsDownstreamController');
Route::resource('DeviceType', 'DeviceTypeController');
Route::resource('Device', 'DeviceController');
Route::get('Device/{modem}/controlling', array ('as' => 'Device.controlling_edit', 'uses' => 'DeviceController@controlling_edit'));
Route::put('Device/{modem}/controlling', array ('as' => 'Device.controlling_update', 'uses' => 'DeviceController@controlling_update'));

Route::resource('Phonenumber', 'PhonenumberController');
Route::resource('Mta', 'MtaController');
