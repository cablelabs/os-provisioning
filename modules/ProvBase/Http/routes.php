<?php

BaseRoute::group([], function() {

	BaseRoute::resource('Modem', 'Modules\ProvBase\Http\Controllers\ModemController');
	BaseRoute::resource('Cmts', 'Modules\ProvBase\Http\Controllers\CmtsController');
	BaseRoute::resource('IpPool', 'Modules\ProvBase\Http\Controllers\IpPoolController');
	BaseRoute::resource('Endpoint', 'Modules\ProvBase\Http\Controllers\EndpointController');
	BaseRoute::resource('Configfile', 'Modules\ProvBase\Http\Controllers\ConfigfileController');
	BaseRoute::resource('Qos', 'Modules\ProvBase\Http\Controllers\QosController');
	BaseRoute::resource('Contract', 'Modules\ProvBase\Http\Controllers\ContractController');
	BaseRoute::resource('Domain', 'Modules\ProvBase\Http\Controllers\DomainController');
	BaseRoute::resource('ProvBase', 'Modules\ProvBase\Http\Controllers\ProvBaseController');

	BaseRoute::get('modem/{modem}/ping', [
		'as' => 'Modem.ping',
		'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@ping',
		'middleware' => ['can:edit,Modules\ProvBase\Entities\Modem'],
	]);

	BaseRoute::get('modem/{modem}/monitoring', [
		'as' => 'Modem.monitoring',
		'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@monitoring',
		'middleware' => ['can:edit,Modules\ProvBase\Entities\Modem'],
	]);

	BaseRoute::get('modem/{modem}/log', [
		'as' => 'Modem.log',
		'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@log',
		'middleware' => ['can:edit,Modules\ProvBase\Entities\Modem'],
	]);

	BaseRoute::get('modem/{modem}/lease', [
		'as' => 'Modem.lease',
		'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@lease',
		'middleware' => ['can:update,Modules\ProvBase\Entities\Modem'],
	]);

});
