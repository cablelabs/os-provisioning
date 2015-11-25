<?php

Route::group(['prefix' => 'hfccustomer', 'namespace' => 'Modules\HfcCustomer\Http\Controllers'], function()
{
	Route::get('/', 'HfcCustomerController@index');
});