<?php

BaseRoute::group([], function() {

	BaseRoute::resource('MibFile', 'Modules\HfcSnmp\Http\Controllers\MibFileController');
	BaseRoute::resource('OID', 'Modules\HfcSnmp\Http\Controllers\OIDController');
	BaseRoute::resource('Parameter', 'Modules\HfcSnmp\Http\Controllers\ParameterController');
	BaseRoute::resource('SnmpValue', 'Modules\HfcSnmp\Http\Controllers\SnmpValueController');

	BaseRoute::get('Parameter/{param}/assign', array('as' => 'Parameter.assign', 'uses' => 'Modules\HfcSnmp\Http\Controllers\ParameterController@assign'));
	BaseRoute::post('Parameter/{param}/attach_oids', array('as' => 'Parameter.attach_oids', 'uses' => 'Modules\HfcSnmp\Http\Controllers\ParameterController@attach_oids'));
	\Route::delete('Parameter/{param}/detach_all', array('as' => 'Parameter.detach_all', 'uses' => 'Modules\HfcSnmp\Http\Controllers\ParameterController@detach_all', 'middleware' => 'auth:delete'));

});
