<?php

Route::group(['prefix' => 'provvoipenvia', 'namespace' => 'Modules\ProvVoipEnvia\Http\Controllers'], function()
{
	Route::get('/', 'ProvVoipEnviaController@index');
});