<?php

// Authentification is necessary before accessing a route
Route::group(array('before' => 'auth'), function() {

	Route::get('/provvoipenvia/ping', array('as' => 'ProvVoipEnvia.ping', 'uses' => 'Modules\ProvVoipEnvia\Http\Controllers\ProvVoipEnviaController@ping'));

});
