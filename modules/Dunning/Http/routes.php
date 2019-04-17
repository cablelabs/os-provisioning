<?php

Route::group(['middleware' => 'web', 'prefix' => 'dunning', 'namespace' => 'Modules\Dunning\Http\Controllers'], function()
{
    Route::get('/', 'DunningController@index');
});
