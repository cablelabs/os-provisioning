<?php

// Authentification is necessary before accessing a route
Route::group(array('before' => 'auth'), function() {

	// Modem
	Route::get('modem/{modem}/ping', array ('as' => 'Modem.ping', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@ping'));
	Route::get('modem/{modem}/monitoring', array ('as' => 'Modem.monitoring', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@monitoring'));
	Route::get('modem/{modem}/log', array ('as' => 'Modem.log', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@log'));
	Route::get('modem/{modem}/lease', array ('as' => 'Modem.lease', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@lease'));

	// Core Routes
	CoreRoute::resource('Modem', 'Modules\ProvBase\Http\Controllers\ModemController');
	CoreRoute::resource('Cmts', 'Modules\ProvBase\Http\Controllers\CmtsController');
	CoreRoute::resource('IpPool', 'Modules\ProvBase\Http\Controllers\IpPoolController');
	CoreRoute::resource('Endpoint', 'Modules\ProvBase\Http\Controllers\EndpointController');
	CoreRoute::resource('Configfile', 'Modules\ProvBase\Http\Controllers\ConfigfileController');
	CoreRoute::resource('Qos', 'Modules\ProvBase\Http\Controllers\QosController');
	CoreRoute::resource('Contract', 'Modules\ProvBase\Http\Controllers\ContractController');
	CoreRoute::resource('ProvBase', 'Modules\ProvBase\Http\Controllers\ProvBaseController');

	// Config File Hierarchical Index Page
	Route::get('Configfile_t', ['as' => 'Configfile.tree', 'uses' => 'Modules\ProvBase\Http\Controllers\ConfigfileController@index_tree']);
});
