<?php

CoreRoute::group([], function() {

	CoreRoute::resource('SnmpMib', 'Modules\HfcSnmp\Http\Controllers\SnmpMibController');
	CoreRoute::resource('SnmpValue', 'Modules\HfcSnmp\Http\Controllers\SnmpValueController');
	CoreRoute::resource('DeviceType', 'Modules\HfcSnmp\Http\Controllers\DeviceTypeController');
	CoreRoute::resource('Device', 'Modules\HfcSnmp\Http\Controllers\DeviceController');


	Route::get('Device/{modem}/controlling', array ('as' => 'Device.controlling_edit', 'uses' => 'Modules\HfcSnmp\Http\Controllers\DeviceController@controlling_edit'));
	Route::put('Device/{modem}/controlling', array ('as' => 'Device.controlling_update', 'uses' => 'Modules\HfcSnmp\Http\Controllers\DeviceController@controlling_update'));

	Route::get('Device/{modem}/controlling', array ('as' => 'Device.controlling_edit', 'uses' => 'Modules\HfcSnmp\Http\Controllers\DeviceController@controlling_edit'));
	Route::put('Device/{modem}/controlling', array ('as' => 'Device.controlling_update', 'uses' => 'Modules\HfcSnmp\Http\Controllers\DeviceController@controlling_update'));

});
