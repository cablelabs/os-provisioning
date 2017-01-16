<?php

BaseRoute::group([], function() {

	BaseRoute::resource('NetElementType', 'Modules\HfcReq\Http\Controllers\NetElementTypeController');
	BaseRoute::resource('NetElement', 'Modules\HfcReq\Http\Controllers\NetElementController');

	// extra View
	BaseRoute::get('NetElementType/{netelementtype}/assign', array ('as' => 'NetElementType.assign', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@assign'));
	// BaseRoute::post('NetElementType/{netelementtype}/assignoids', array ('as' => 'NetElementType.assign_oids', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@add_oid_list'));

	// attach & detach routes for many-to-many relationship
	BaseRoute::post('NetElementType/{netelementtype}/attach_oids', array ('as' => 'NetElementType.attach_oids', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@attach_oids'));
	\Route::delete('NetElementType/{netelementtype}/detach_all', array('as' => 'NetElementType.detach_all', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@detach_all', 'middleware' => 'auth:delete'));
	// \Route::delete('NetElementType/{netelementtype}/detach', array('as' => 'NetElementType.detach_oid', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@detach', 'middleware' => 'auth:delete'));

	BaseRoute::get('NetElement/{id}/controlling', array ('as' => 'NetElement.controlling_edit', 'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpController@controlling_edit'));
	BaseRoute::put('NetElement/{id}/controlling', array ('as' => 'NetElement.controlling_update', 'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpController@controlling_update'));

});