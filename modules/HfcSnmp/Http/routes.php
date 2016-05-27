<?php

BaseRoute::group([], function() {

	BaseRoute::resource('SnmpMib', 'Modules\HfcSnmp\Http\Controllers\SnmpMibController');
	BaseRoute::resource('SnmpValue', 'Modules\HfcSnmp\Http\Controllers\SnmpValueController');
	BaseRoute::resource('DeviceType', 'Modules\HfcSnmp\Http\Controllers\DeviceTypeController');
	BaseRoute::resource('Device', 'Modules\HfcSnmp\Http\Controllers\DeviceController');


	BaseRoute::get('Device/{modem}/controlling', array ('as' => 'Device.controlling_edit', 'uses' => 'Modules\HfcSnmp\Http\Controllers\DeviceController@controlling_edit'));
	BaseRoute::put('Device/{modem}/controlling', array ('as' => 'Device.controlling_update', 'uses' => 'Modules\HfcSnmp\Http\Controllers\DeviceController@controlling_update'));

});
