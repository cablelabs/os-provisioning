<?php

// Authentification is necessary before accessing a route
Route::group(array('before' => 'auth'), function() {

	Route::resource('SnmpMib', 'Modules\HfcSnmp\Http\Controllers\SnmpMibController');
	Route::resource('SnmpValue', 'Modules\HfcSnmp\Http\Controllers\SnmpValueController');
	Route::resource('DeviceType', 'Modules\HfcSnmp\Http\Controllers\DeviceTypeController');
	Route::resource('Device', 'Modules\HfcSnmp\Http\Controllers\DeviceController');


	Route::get('Device/{modem}/controlling', array ('as' => 'Device.controlling_edit', 'uses' => 'Modules\HfcSnmp\Http\Controllers\DeviceController@controlling_edit'));
	Route::put('Device/{modem}/controlling', array ('as' => 'Device.controlling_update', 'uses' => 'Modules\HfcSnmp\Http\Controllers\DeviceController@controlling_update'));


	Route::get('device/fulltextSearch', array('as' => 'Device.fulltextSearch', 'uses' => 'Modules\HfcSnmp\Http\Controllers\DeviceController@fulltextSearch'));
	Route::get('devicetype/fulltextSearch', array('as' => 'DeviceType.fulltextSearch', 'uses' => 'Modules\HfcSnmp\Http\Controllers\DeviceTypeController@fulltextSearch'));
	Route::get('snmpmib/fulltextSearch', array('as' => 'SnmpMib.fulltextSearch', 'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpMibController@fulltextSearch'));
	Route::get('snmpvalue/fulltextSearch', array('as' => 'SnmpValue.fulltextSearch', 'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpValueController@fulltextSearch'));

	Route::get('Device/{modem}/controlling', array ('as' => 'Device.controlling_edit', 'uses' => 'Modules\HfcSnmp\Http\Controllers\DeviceController@controlling_edit'));
	Route::put('Device/{modem}/controlling', array ('as' => 'Device.controlling_update', 'uses' => 'Modules\HfcSnmp\Http\Controllers\DeviceController@controlling_update'));

});
