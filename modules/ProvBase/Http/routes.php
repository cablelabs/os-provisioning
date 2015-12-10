<?php

// Authentification is necessary before accessing a route
Route::group(array('before' => 'auth'), function() {

	Route::get('', 'Modules\ProvBase\Http\Controllers\ModemController@index');

	// Modem
	Route::get('modem/{modem}/ping', array ('as' => 'Modem.ping', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@ping'));
	Route::get('modem/{modem}/monitoring', array ('as' => 'Modem.monitoring', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@monitoring'));
	// array (name, function of Controller)
	Route::get('modem/{modem}/log', array ('as' => 'Modem.log', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@log'));
	Route::get('modem/{modem}/lease', array ('as' => 'Modem.lease', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@lease'));
	Route::post('modem/json', 'Modules\ProvBase\Http\Controllers\ModemController@json');

	// Searches
	Route::get('modem/fulltextSearch', array('as' => 'Modem.fulltextSearch', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@fulltextSearch'));
	Route::get('cmts/fulltextSearch', array('as' => 'Cmts.fulltextSearch', 'uses' => 'Modules\ProvBase\Http\Controllers\CmtsController@fulltextSearch'));
	Route::get('ippool/fulltextSearch', array('as' => 'IpPool.fulltextSearch', 'uses' => 'Modules\ProvBase\Http\Controllers\IpPoolController@fulltextSearch'));
	Route::get('endpoint/fulltextSearch', array('as' => 'Endpoint.fulltextSearch', 'uses' => 'Modules\ProvBase\Http\Controllers\EndpointController@fulltextSearch'));
	Route::get('configfile/fulltextSearch', array('as' => 'Configfile.fulltextSearch', 'uses' => 'Modules\ProvBase\Http\Controllers\ConfigfileController@fulltextSearch'));
	Route::get('qos/fulltextSearch', array('as' => 'Qos.fulltextSearch', 'uses' => 'Modules\ProvBase\Http\Controllers\QosController@fulltextSearch'));
	Route::get('contract/fulltextSearch', array('as' => 'Contract.fulltextSearch', 'uses' => 'Modules\ProvBase\Http\Controllers\ContractController@fulltextSearch'));


	// routes controller with predefined methods
	// add array('only' => array('edit', 'update')) as third parameter to only allow these routes

	Route::resource('Modem', 'Modules\ProvBase\Http\Controllers\ModemController');
	Route::resource('Cmts', 'Modules\ProvBase\Http\Controllers\CmtsController');
	Route::resource('IpPool', 'Modules\ProvBase\Http\Controllers\IpPoolController');
	Route::resource('Endpoint', 'Modules\ProvBase\Http\Controllers\EndpointController');
	#Route::post('endpoint/json', 'Modules\ProvBase\Http\Controllers\EndpointController@json');
	Route::resource('Configfile', 'Modules\ProvBase\Http\Controllers\ConfigfileController');
	Route::resource('Qos', 'Modules\ProvBase\Http\Controllers\QosController');
	Route::resource('Contract', 'Modules\ProvBase\Http\Controllers\ContractController');

});
