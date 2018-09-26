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
        'middleware' => ['can:delete,Modules\HfcSnmp\Entities\Parameter'],
    ]);
});
