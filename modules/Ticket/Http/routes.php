<?php

Route::group(['middleware' => 'web', 'prefix' => 'ticket', 'namespace' => 'Modules\Ticket\Http\Controllers'], function()
{
	Route::get('/', 'TicketController@index');
});