<?php

namespace Modules\Dunning\Entities;

return [
    'name' => 'Dunning',
    'MenuItems' => [
        'Debt' => [
            'link'	=> 'Debt.result',
            'icon'	=> 'fa-usd',
            'class' => Debt::class,
        ],
    ],
];
