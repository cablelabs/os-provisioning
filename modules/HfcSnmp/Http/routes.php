<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
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

    BaseRoute::get('NetElement/{netelement}/controlling/{parameter}/{index}', [
        'as' => 'NetElement.controlling_edit',
        'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpController@controlling_edit',
        'middleware' => ['can:view,Modules\HfcReq\Entities\NetElementType'],
    ]);

    BaseRoute::put('NetElement/{id}/controlling/{parameter}/{index}', [
        'as' => 'NetElement.controlling_update',
        'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpController@controlling_update',
        'middleware' => ['can:update,Modules\HfcReq\Entities\NetElement'],
    ]);

    BaseRoute::post('NetElement/{netelement}/triggerSnmpQueryLoop/{parameter}/{index}', [
        'as' => 'NetElement.triggerSnmpQueryLoop',
        'uses' => 'Modules\HfcSnmp\Http\Controllers\SnmpController@triggerSnmpQueryLoop',
        'middleware' => ['can:view,Modules\HfcReq\Entities\NetElement'],
    ]);
});
