<?php

Route::resource('Phonenumber', 'Modules\ProvVoip\Http\Controllers\PhonenumberController');
Route::resource('Mta', 'Modules\ProvVoip\Http\Controllers\MtaController');

Route::get('phonenumber/fulltextSearch', array('as' => 'Phonenumber.fulltextSearch', 'uses' => 'Modules\ProvVoip\Http\Controllers\PhonenumberController@fulltextSearch'));
Route::get('mta/fulltextSearch', array('as' => 'Mta.fulltextSearch', 'uses' => 'Modules\ProvVoip\Http\Controllers\MtaController@fulltextSearch'));
