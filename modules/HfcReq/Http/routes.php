<?php

BaseRoute::group([], function() {

	BaseRoute::resource('NetElementType', 'Modules\HfcReq\Http\Controllers\NetElementTypeController');
	BaseRoute::resource('NetElement', 'Modules\HfcReq\Http\Controllers\NetElementController');

	BaseRoute::post('NetElementType/{netelementtype}/assignoids', array ('as' => 'NetElementType.assign_oids', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@add_oid_from_mib'));

	BaseRoute::get('NetElementType/{netelementtype}/assignoids', array ('as' => 'NetElementType.assign_oids', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@add_oid_from_mib'));

	// attach & detach routes for many-to-many relationship
	BaseRoute::get('NetElementType/{netelementtype}/assign', array ('as' => 'NetElementType.assign', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@assign'));
	BaseRoute::post('NetElementType/{netelementtype}/attach', array ('as' => 'NetElementType.attach', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@attach'));
	\Route::delete('NetElementType/{netelementtype}/detach', array('as' => 'NetElementType.detach_oid', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@detach', 'middleware' => 'auth:delete'));
	\Route::delete('NetElementType/{netelementtype}/detach_all', array('as' => 'NetElementType.detach_all', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@detach_all', 'middleware' => 'auth:delete'));

	BaseRoute::get('NetElement/{modem}/controlling', array ('as' => 'NetElement.controlling_edit', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementController@controlling_edit'));
	BaseRoute::put('NetElement/{modem}/controlling', array ('as' => 'NetElement.controlling_update', 'uses' => 'Modules\HfcReq\Http\Controllers\NetElementController@controlling_update'));

});