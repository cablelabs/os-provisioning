<?php

BaseRoute::group([], function() {

	BaseRoute::resource('NetElementType', 'Modules\HfcReq\Http\Controllers\NetElementTypeController');
	BaseRoute::resource('NetElement', 'Modules\HfcReq\Http\Controllers\NetElementController');
	BaseRoute::resource('Indices', 'Modules\HfcSnmp\Http\Controllers\IndicesController');

	// extra View
	BaseRoute::get('NetElementType/{netelementtype}/assign', array ('as' => 'NetElementType.assign', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@assign'));
	BaseRoute::post('NetElementType/{netelementtype}/settings', array ('as' => 'NetElementType.settings', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@settings'));

	// attach & detach routes for many-to-many relationship
	BaseRoute::post('NetElementType/{netelementtype}/attach_oids', array ('as' => 'NetElementType.attach_oids', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@attach_oids'));
	\Route::delete('NetElementType/{netelementtype}/detach_all', array('as' => 'NetElementType.detach_all', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@detach_all', 'middleware' => 'auth:delete'));
	// \Route::delete('NetElementType/{netelementtype}/detach', array('as' => 'NetElementType.detach_oid', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@detach', 'middleware' => 'auth:delete'));

	BaseRoute::get('NetElement/{id}/delete', array('as' => 'NetElement.delete', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementController@destroy'));
	BaseRoute::get('NetElement/{id}/controlling/{parameter}/{index}', array ('as' => 'NetElement.controlling_edit', 'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpController@controlling_edit'));
	BaseRoute::put('NetElement/{id}/controlling/{parameter}/{index}', array ('as' => 'NetElement.controlling_update', 'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpController@controlling_update'));
	BaseRoute::get('NetElement/{id}/sse_get_snmpvalues/{parameter}/{index}/{reload}', array ('as' => 'NetElement.sse_get_snmpvalues', 'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpController@sse_get_snmpvalues'));
});
