<?php

BaseRoute::group([], function() {

	BaseRoute::resource('Email', 'Modules\Mail\Http\Controllers\EmailController');

});
