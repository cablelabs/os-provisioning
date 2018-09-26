<?php

BaseRoute::group([], function () {
    BaseRoute::resource('NetElementType', 'Modules\HfcReq\Http\Controllers\NetElementTypeController');
    BaseRoute::resource('NetElement', 'Modules\HfcReq\Http\Controllers\NetElementController');

    BaseRoute::get('NetElementType/{netelementtype}/assign', [
        'as' => 'NetElementType.assign',
        'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@assign',
        'middleware' => [
            'can:update,Modules\HfcReq\Entities\NetElementType',
            'can:update,Modules\HfcReq\Entities\NetElement',
        ],
    ]);

    BaseRoute::post('NetElementType/{netelementtype}/settings', [
        'as' => 'NetElementType.settings',
        'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@settings',
        'middleware' => ['can:update,Modules\HfcReq\Entities\NetElementType'],
    ]);

    // attach & detach routes for many-to-many relationship
    BaseRoute::post('NetElementType/{netelementtype}/attach_oids', [
        'as' => 'NetElementType.attach_oids',
        'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@attach_oids',
        'middleware' => ['can:update,Modules\HfcReq\Entities\NetElementType'],
    ]);

    Route::delete('NetElementType/{netelementtype}/detach_all', [
        'as' => 'NetElementType.detach_all',
        'uses' => 'Modules\HfcReq\Http\Controllers\NetElementTypeController@detach_all',
        'middleware' => ['can:delete,Modules\HfcReq\Entities\NetElementType'],
    ]);

    BaseRoute::get('NetElement/{id}/delete', [
        'as' => 'NetElement.delete',
        'uses' => 'Modules\HfcReq\Http\Controllers\NetElementController@destroy',
        'middleware' => ['can:delete,Modules\HfcReq\Entities\NetElement'],
    ]);

    BaseRoute::get('NetElement/{id}/controlling/{parameter}/{index}', [
        'as' => 'NetElement.controlling_edit',
        'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpController@controlling_edit',
        'middleware' => ['can:view,Modules\HfcReq\Entities\NetElementType'],
    ]);

    BaseRoute::put('NetElement/{id}/controlling/{parameter}/{index}', [
        'as' => 'NetElement.controlling_update',
        'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpController@controlling_update',
        'middleware' => ['can:update,Modules\HfcReq\Entities\NetElement'],
    ]);

    BaseRoute::get('NetElement/{id}/sse_get_snmpvalues/{parameter}/{index}/{reload}', [
        'as' => 'NetElement.sse_get_snmpvalues',
        'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpController@sse_get_snmpvalues',
        'middleware' => ['can:update,Modules\HfcReq\Entities\NetElement'],
    ]);
});
