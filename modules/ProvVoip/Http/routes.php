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
            'middleware' => ['api', 'can:update,Modules\ProvVoip\Entities\Mta'],
        ]);
    });
});
