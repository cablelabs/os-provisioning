<?php

Route::group(['prefix' => 'hfcbase', 'namespace' => 'Modules\HfcBase\Http\Controllers'], function()
{
	Route::get('/', 'HfcBaseController@index');
});