<?php

BaseRoute::group([], function () {

    BaseRoute::resource('Dunning', 'Modules\Dunning\Http\Controllers\DunningController');
    BaseRoute::resource('Debt', 'Modules\Dunning\Http\Controllers\DebtController');
});
