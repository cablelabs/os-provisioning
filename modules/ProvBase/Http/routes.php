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
    BaseRoute::resource('Modem', 'Modules\ProvBase\Http\Controllers\ModemController');
    BaseRoute::resource('NetGw', 'Modules\ProvBase\Http\Controllers\NetGwController');
    BaseRoute::resource('IpPool', 'Modules\ProvBase\Http\Controllers\IpPoolController');
    BaseRoute::resource('Endpoint', 'Modules\ProvBase\Http\Controllers\EndpointController');
    BaseRoute::resource('Configfile', 'Modules\ProvBase\Http\Controllers\ConfigfileController');
    BaseRoute::resource('Qos', 'Modules\ProvBase\Http\Controllers\QosController');
    BaseRoute::resource('Contract', 'Modules\ProvBase\Http\Controllers\ContractController');
    BaseRoute::resource('Domain', 'Modules\ProvBase\Http\Controllers\DomainController');
    BaseRoute::resource('ProvBase', 'Modules\ProvBase\Http\Controllers\ProvBaseController');
    BaseRoute::resource('ModemOption', 'Modules\ProvBase\Http\Controllers\ModemOptionController');

    BaseRoute::get('modem/firmware', [
        'as' => 'Modem.firmware',
        'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@firmware_view',
        'middleware' => ['can:view,Modules\ProvBase\Entities\Modem'],
    ]);

    BaseRoute::get('modem/cwmp', [
        'as' => 'Modem.cwmp',
        'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@cwmpDeviceView',
        'middleware' => ['can:view,Modules\ProvBase\Entities\Modem'],
    ]);

    BaseRoute::post('Modem/genietask/{id}', [
        'as' => 'Modem.genieTask',
        'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@genieTask',
        'middleware' => ['can:update,Modules\ProvBase\Entities\Modem'],
    ]);

    BaseRoute::get('modem/autocomplete/mac', [
        'as' => 'Modem.unknownMACAddresses',
        'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@unknownMACAddresses',
        'middleware' => ['can:view,Modules\ProvBase\Entities\Modem'],
    ]);

    BaseRoute::get('Configfile/{id}/refreshgenieacs', [
        'as' => 'Configfile.refreshGenieAcs',
        'uses' => 'Modules\ProvBase\Http\Controllers\ConfigfileController@refreshGenieAcs',
        'middleware' => ['can:update,Modules\ProvBase\Entities\Configfile'],
    ]);

    BaseRoute::get('Configfile/{id}/searchdeviceparams', [
        'as' => 'Configfile.searchDeviceParams',
        'uses' => 'Modules\ProvBase\Http\Controllers\ConfigfileController@searchDeviceParams',
        'middleware' => ['can:update,Modules\ProvBase\Entities\Configfile'],
    ]);

    Route::group(['prefix' => 'api/v{ver}'], function () {
        Route::get('modem/geo-pos', [
            'as' => 'Modem.apiGeoPos',
            'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@apiGeoPos',
            'middleware' => ['api', 'can:view,Modules\ProvBase\Entities\Modem'],
        ]);
        Route::get('modem/{Modem}/same-location-modems', [
            'as' => 'Modem.apiGetModemsOfSameLocation',
            'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@apiGetModemsOfSameLocation',
            'middleware' => ['api', 'can:update,Modules\ProvBase\Entities\Modem'],
        ]);
        Route::get('Modem/{Modem}/restart', [
            'as' => 'Modem.api_restart',
            'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@api_restart',
            'middleware' => ['api', 'can:update,Modules\ProvBase\Entities\Modem'],
        ]);

        Route::get('Modem/{id}/blockDhcp', [
            'as' => 'Modem.api_blockDhcp',
            'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@api_blockDhcp',
            'middleware' => ['api', 'can:update,Modules\ProvBase\Entities\Modem'],
        ]);

        Route::get('Modem/{id}/unblockDhcp', [
            'as' => 'Modem.api_unblockDhcp',
            'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@api_unblockDhcp',
            'middleware' => ['api', 'can:update,Modules\ProvBase\Entities\Modem'],
        ]);

        Route::post('Modem/{id}/setDns', [
            'as' => 'Modem.api_setDns',
            'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@api_setDns',
            'middleware' => ['api', 'can:update,Modules\ProvBase\Entities\Modem'],
        ]);

        Route::get('Modem/{id}/unsetDns', [
            'as' => 'Modem.api_unsetDns',
            'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@api_unsetDns',
            'middleware' => ['api', 'can:update,Modules\ProvBase\Entities\Modem'],
        ]);
    });

    BaseRoute::get('modem/{id}/analysis', [
        'as' => 'Modem.analysis',
        'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@analysis',
        'middleware' => ['can:view,Modules\ProvBase\Entities\Modem'],
    ]);

    BaseRoute::get('modem/{id}/cpeAnalysis', [
        'as' => 'Modem.cpeAnalysis',
        'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@cpeAnalysis',
        'middleware' => ['can:view_analysis_pages_of,Modules\ProvBase\Entities\Modem'],
    ]);

    BaseRoute::get('modem/{id}/mtaAnalysis', [
        'as' => 'Modem.mtaAnalysis',
        'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@mtaAnalysis',
        'middleware' => ['can:view_analysis_pages_of,Modules\ProvBase\Entities\Modem'],
    ]);

    BaseRoute::post('modem/{modem}/floodPing', [
        'as' => 'Modem.floodPing',
        'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@floodPing',
        'middleware' => ['can:view_analysis_pages_of,Modules\ProvBase\Entities\Modem'],
    ]);

    BaseRoute::get('modem/ping/{ip}', [
        'as' => 'Modem.realtimePing',
        'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@realtimePing',
        'middleware' => ['can:view_analysis_pages_of,Modules\ProvBase\Entities\Modem'],
    ]);
});
