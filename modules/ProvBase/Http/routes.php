<?php

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

    BaseRoute::get('modem/firmware', [
        'as' => 'Modem.firmware',
        'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@firmware_view',
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
        Route::get('Modem/{Modem}/restart', [
            'as' => 'Modem.api_restart',
            'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@api_restart',
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

    BaseRoute::post('modem/{id}/floodPing', [
        'as' => 'Modem.floodPing',
        'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@analysis',
        'middleware' => ['can:view_analysis_pages_of,Modules\ProvBase\Entities\Modem'],
    ]);

    BaseRoute::get('modem/ping/{ip}', [
        'as' => 'Modem.realtimePing',
        'uses' => 'Modules\ProvBase\Http\Controllers\ModemController@realtimePing',
        'middleware' => ['can:view_analysis_pages_of,Modules\ProvBase\Entities\Modem'],
    ]);

    BaseRoute::get('missingProvMon', [
        'as' => 'missingProvMon',
        'uses' => 'Modules\ProvBase\Http\Controllers\NetGwController@missingProvMon',
        'middleware' => ['can:view,Modules\ProvBase\Entities\NetGw'],
    ]);
});
