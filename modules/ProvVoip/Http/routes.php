<?php

BaseRoute::group([], function() {

	BaseRoute::resource('PhonenumberManagement', 'Modules\ProvVoip\Http\Controllers\PhonenumberManagementController');
	BaseRoute::resource('Phonenumber', 'Modules\ProvVoip\Http\Controllers\PhonenumberController');
	BaseRoute::resource('Mta', 'Modules\ProvVoip\Http\Controllers\MtaController');
	BaseRoute::resource('ProvVoip', 'Modules\ProvVoip\Http\Controllers\ProvVoipController');

});
