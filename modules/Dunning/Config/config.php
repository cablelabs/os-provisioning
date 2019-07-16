<?php

namespace Modules\Dunning\Entities;

return [
    'name' => trans('dunning::view.menu.Dunning'),
    'MenuItems' => [
        'Debt' => [
            'link'	=> 'Debt.result',
            'icon'	=> 'fa-usd',
            'class' => Debt::class,
        ],
    ],
];
