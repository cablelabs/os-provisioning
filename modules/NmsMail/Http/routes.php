<?php

BaseRoute::group([], function () {
    BaseRoute::resource('Email', 'Modules\NmsMail\Http\Controllers\EmailController');
});
