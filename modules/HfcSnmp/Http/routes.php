<?php

BaseRoute::group([], function() {

	BaseRoute::resource('MibFile', 'Modules\HfcSnmp\Http\Controllers\MibFileController');
	BaseRoute::resource('OID', 'Modules\HfcSnmp\Http\Controllers\OIDController');
	BaseRoute::resource('SnmpValue', 'Modules\HfcSnmp\Http\Controllers\SnmpValueController');

});
