<?php

CoreRoute::group([], function() {

	CoreRoute::resource('PhonenumberManagement', 'Modules\ProvVoip\Http\Controllers\PhonenumberManagementController');
	CoreRoute::resource('Phonenumber', 'Modules\ProvVoip\Http\Controllers\PhonenumberController');
	CoreRoute::resource('Mta', 'Modules\ProvVoip\Http\Controllers\MtaController');
	CoreRoute::resource('ProvVoip', 'Modules\ProvVoip\Http\Controllers\ProvVoipController');

});
