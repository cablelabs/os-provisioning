<?php

BaseRoute::group([], function () {
    BaseRoute::resource('Debt', 'Modules\Dunning\Http\Controllers\DebtController');
});
