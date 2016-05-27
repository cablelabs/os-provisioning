<?php

BaseRoute::group([], function() {

	// Modem
	BaseRoute::get('modem/{modem}/ping', array ('as' => 'Modem.ping', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@ping'));
	BaseRoute::get('modem/{modem}/monitoring', array ('as' => 'Modem.monitoring', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@monitoring'));
	BaseRoute::get('modem/{modem}/log', array ('as' => 'Modem.log', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@log'));
	BaseRoute::get('modem/{modem}/lease', array ('as' => 'Modem.lease', 'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@lease'));

	// Core Routes
	BaseRoute::resource('Modem', 'Modules\ProvBase\Http\Controllers\ModemController');
	BaseRoute::resource('Cmts', 'Modules\ProvBase\Http\Controllers\CmtsController');
	BaseRoute::resource('IpPool', 'Modules\ProvBase\Http\Controllers\IpPoolController');
	BaseRoute::resource('Endpoint', 'Modules\ProvBase\Http\Controllers\EndpointController');
	BaseRoute::resource('Configfile', 'Modules\ProvBase\Http\Controllers\ConfigfileController');
	BaseRoute::resource('Qos', 'Modules\ProvBase\Http\Controllers\QosController');
	BaseRoute::resource('Contract', 'Modules\ProvBase\Http\Controllers\ContractController');
	BaseRoute::resource('ProvBase', 'Modules\ProvBase\Http\Controllers\ProvBaseController');

	// Configfile: Hierarchical Index Page
	BaseRoute::get('Configfile_t', ['as' => 'Configfile.tree', 'uses' => 'Modules\ProvBase\Http\Controllers\ConfigfileController@index_tree']);

	// Contract: Download Connection Info
	BaseRoute::get('contract/conn_info/{id}', array('as' => 'Contract.ConnInfo', 'uses' => 'Modules\ProvBase\Http\Controllers\ContractController@connection_info_download'));
});
