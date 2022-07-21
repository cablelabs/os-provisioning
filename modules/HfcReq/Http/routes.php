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
    BaseRoute::resource('HfcReq', 'Modules\HfcReq\Http\Controllers\HfcReqController');
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
        'middleware' => ['web', 'can:delete,Modules\HfcReq\Entities\NetElementType'],
    ]);

    BaseRoute::post('Netelement/{netelement}/favorite', [
        'as' => 'NetElement.favorite',
        'uses' => 'Modules\HfcReq\Http\Controllers\NetElementController@favorite',
    ]);

    BaseRoute::post('Netelement/{netelement}/unfavorite', [
        'as' => 'NetElement.unfavorite',
        'uses' => 'Modules\HfcReq\Http\Controllers\NetElementController@unfavorite',
    ]);

    BaseRoute::post('Netelement/netclustersearch', [
        'as' => 'NetElement.searchNetsClusters',
        'uses' => 'Modules\HfcReq\Http\Controllers\NetElementController@searchForNetsAndClusters',
    ]);

    BaseRoute::post('Netelement/{netelement}/clustersearch', [
        'as' => 'NetElement.searchClusters',
        'uses' => 'Modules\HfcReq\Http\Controllers\NetElementController@searchClusters',
    ]);
});
