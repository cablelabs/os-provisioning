<?php

BaseRoute::group([], function () {

    // Note: Defining this route after resource() results in Laravel calling the edit route with param 'result' (as $id) as the request URI matches this route first in the route definitions
    BaseRoute::get('Debt/result', [
        'as' => 'Debt.result',
        'uses' => 'Modules\Dunning\Http\Controllers\DebtController@result',
        'middleware' => ['can:view,Modules\Dunning\Entities\Debt'],
    ]);

    BaseRoute::resource('Dunning', 'Modules\Dunning\Http\Controllers\DunningController');
    BaseRoute::resource('Debt', 'Modules\Dunning\Http\Controllers\DebtController');

    Route::get('Debt/result/datatables', [
        'as' => 'Debt.result.data',
        'uses' => 'Modules\Dunning\Http\Controllers\DebtController@result_datatables_ajax',
        'middleware' => ['web', 'can:view,Modules\Dunning\Entities\Debt'],
    ]);
});
