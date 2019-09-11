<?php

BaseRoute::group([], function () {

    // Note: Defining this route after resource() results in Laravel calling the edit route with param 'result' (as $id) as the request URI matches this route first in the route definitions
    BaseRoute::get('Debt/result', [
        'as' => 'Debt.result',
        'uses' => 'Modules\OverdueDebts\Http\Controllers\DebtController@result',
        'middleware' => ['can:view,Modules\OverdueDebts\Entities\Debt'],
    ]);

    BaseRoute::resource('OverdueDebts', 'Modules\OverdueDebts\Http\Controllers\OverdueDebtsController');
    BaseRoute::resource('Debt', 'Modules\OverdueDebts\Http\Controllers\DebtController');

    Route::get('Debt/result/datatables', [
        'as' => 'Debt.result.data',
        'uses' => 'Modules\OverdueDebts\Http\Controllers\DebtController@result_datatables_ajax',
        'middleware' => ['web', 'can:view,Modules\OverdueDebts\Entities\Debt'],
    ]);
});
