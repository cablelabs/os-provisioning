<?php

// Authentification is necessary before accessing a route
Route::group(array('before' => 'auth'), function() {

	Route::resource('Mta', 'Modules\ProvVoip\Http\Controllers\MtaController');
	Route::resource('PhonebookEntry', 'Modules\ProvVoip\Http\Controllers\PhonebookEntryController');
	Route::resource('Phonenumber', 'Modules\ProvVoip\Http\Controllers\PhonenumberController');
	Route::resource('PhonenumberManagement', 'Modules\ProvVoip\Http\Controllers\PhonenumberManagementController');
	Route::resource('PhoneTariff', 'Modules\ProvVoip\Http\Controllers\PhoneTariffController');
	Route::resource('ProvVoip', 'Modules\ProvVoip\Http\Controllers\ProvVoipController');

	Route::get('mta/fulltextSearch', array('as' => 'Mta.fulltextSearch', 'uses' => 'Modules\ProvVoip\Http\Controllers\MtaController@fulltextSearch'));
	Route::get('phonebookentry/fulltextSearch', array('as' => 'PhonebookEntry.fulltextSearch', 'uses' => 'Modules\ProvVoip\Http\Controllers\PhonebookEntryController@fulltextSearch'));
	Route::get('phonenumber/fulltextSearch', array('as' => 'Phonenumber.fulltextSearch', 'uses' => 'Modules\ProvVoip\Http\Controllers\PhonenumberController@fulltextSearch'));
	Route::get('phonetariff/fulltextSearch', array('as' => 'PhoneTariff.fulltextSearch', 'uses' => 'Modules\ProvVoip\Http\Controllers\PhoneTariffController@fulltextSearch'));

});
