<?php

BaseRoute::group([], function () {
    BaseRoute::resource('Indices', 'Modules\HfcSnmp\Http\Controllers\IndicesController');
    BaseRoute::resource('MibFile', 'Modules\HfcSnmp\Http\Controllers\MibFileController');
    BaseRoute::resource('OID', 'Modules\HfcSnmp\Http\Controllers\OIDController');
    BaseRoute::resource('Parameter', 'Modules\HfcSnmp\Http\Controllers\ParameterController');

    BaseRoute::get('Parameter/{param}/assign', [
        'as' => 'Parameter.assign',
        'uses' => 'Modules\HfcSnmp\Http\Controllers\ParameterController@assign',
        'middleware' => ['can:update,Modules\HfcSnmp\Entities\Parameter'],
    ]);

    BaseRoute::post('Parameter/{param}/attach_oids', [
        'as' => 'Parameter.attach_oids',
        'uses' => 'Modules\HfcSnmp\Http\Controllers\ParameterController@attach_oids',
        'middleware' => ['can:update,Modules\HfcSnmp\Entities\Parameter'],
    ]);

    Route::delete('Parameter/{param}/detach_all', [
        'as' => 'Parameter.detach_all',
        'uses' => 'Modules\HfcSnmp\Http\Controllers\ParameterController@detach_all',
        'middleware' => ['web', 'can:delete,Modules\HfcSnmp\Entities\Parameter'],
    ]);

    BaseRoute::get('NetElement/{id}/tapControlling', [
        'as' => 'NetElement.tapControlling',
        'uses' => 'Modules\HfcSnmp\Http\Controllers\TapController@show',
        'middleware' => ['can:view,Modules\HfcReq\Entities\NetElement'],
    ]);

    BaseRoute::post('NetElement/switchTapState', [
        'as' => 'NetElement.switchTapState',
        'uses' => 'Modules\HfcSnmp\Http\Controllers\TapController@switchTapState',
        'middleware' => ['can:update,Modules\HfcReq\Entities\NetElement'],
    ]);

    BaseRoute::post('NetElement/switchVideoLine', [
        'as' => 'NetElement.switchVideoLine',
        'uses' => 'Modules\HfcSnmp\Http\Controllers\TapController@switchVideoLine',
        'middleware' => ['can:update,Modules\HfcReq\Entities\NetElement'],
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
