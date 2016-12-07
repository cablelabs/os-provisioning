<?php

BaseRoute::group([], function() {

	BaseRoute::resource('SnmpMib', 'Modules\HfcSnmp\Http\Controllers\SnmpMibController');
	BaseRoute::resource('SnmpValue', 'Modules\HfcSnmp\Http\Controllers\SnmpValueController');

});
