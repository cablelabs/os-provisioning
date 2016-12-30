<?php

BaseRoute::group([], function() {

	BaseRoute::resource('NetElementType', 'Modules\HfcReq\Http\Controllers\NetElementTypeController');
	BaseRoute::resource('NetElement', 'Modules\HfcReq\Http\Controllers\NetElementController');
	BaseRoute::resource('DeviceType', 'Modules\HfcReq\Http\Controllers\DeviceTypeController');
	BaseRoute::post('Devicetype/{devicetype}/assignoids', array ('as' => 'DeviceType.assign_oids', 'uses' => 'Modules\HfcReq\Http\Controllers\DeviceTypeController@add_oid_from_mib'));

	BaseRoute::get('Devicetype/{devicetype}/assignoids', array ('as' => 'DeviceType.assign_oids', 'uses' => 'Modules\HfcReq\Http\Controllers\DeviceTypeController@add_oid_from_mib'));
	// attach & detach routes for many-to-many relationship
	BaseRoute::get('Devicetype/{devicetype}/assign', array ('as' => 'DeviceType.assign', 'uses' => 'Modules\HfcReq\Http\Controllers\DeviceTypeController@assign'));
	BaseRoute::post('Devicetype/{devicetype}/attach', array ('as' => 'DeviceType.attach', 'uses' => 'Modules\HfcReq\Http\Controllers\DeviceTypeController@attach'));
	\Route::delete('Devicetype/{devicetype}/detach', array('as' => 'DeviceType.detach_oid', 'uses' => 'Modules\HfcReq\Http\Controllers\DeviceTypeController@detach', 'middleware' => 'auth:delete'));
	\Route::delete('Devicetype/{devicetype}/detach_all', array('as' => 'DeviceType.detach_all', 'uses' => 'Modules\HfcReq\Http\Controllers\DeviceTypeController@detach_all', 'middleware' => 'auth:delete'));

	BaseRoute::get('NetElement/{modem}/controlling', array ('as' => 'NetElement.controlling_edit', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementController@controlling_edit'));
	BaseRoute::put('NetElement/{modem}/controlling', array ('as' => 'NetElement.controlling_update', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementController@controlling_update'));

});