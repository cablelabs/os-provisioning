<?php

BaseRoute::group([], function () {
    BaseRoute::resource('PhonenumberManagement', 'Modules\ProvVoip\Http\Controllers\PhonenumberManagementController');
    BaseRoute::resource('Phonenumber', 'Modules\ProvVoip\Http\Controllers\PhonenumberController');
    BaseRoute::resource('Mta', 'Modules\ProvVoip\Http\Controllers\MtaController');
    BaseRoute::resource('ProvVoip', 'Modules\ProvVoip\Http\Controllers\ProvVoipController');
    BaseRoute::resource('PhonebookEntry', 'Modules\ProvVoip\Http\Controllers\PhonebookEntryController');
    BaseRoute::resource('PhoneTariff', 'Modules\ProvVoip\Http\Controllers\PhoneTariffController');

    Route::group(['prefix' => 'api/v{ver}'], function () {
        Route::get('Mta/{Mta}/restart', [
            'as' => 'Mta.api_restart',
            'uses' => 'Modules\ProvVoip\Http\Controllers\MtaController@api_restart',
            'middleware' => ['api', 'auth.basic', 'can:update,Modules\ProvVoip\Entities\Mta'],
        ]);
    });
});
