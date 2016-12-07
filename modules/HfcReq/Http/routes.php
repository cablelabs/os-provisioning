<?php

BaseRoute::group([], function() {

	BaseRoute::resource('NetElementType', 'Modules\HfcReq\Http\Controllers\NetElementTypeController');
	BaseRoute::resource('NetElement', 'Modules\HfcReq\Http\Controllers\NetElementController');
	BaseRoute::resource('DeviceType', 'Modules\HfcReq\Http\Controllers\DeviceTypeController');

	BaseRoute::get('NetElement/{modem}/controlling', array ('as' => 'NetElement.controlling_edit', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementController@controlling_edit'));
	BaseRoute::put('NetElement/{modem}/controlling', array ('as' => 'NetElement.controlling_update', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementController@controlling_update'));

});