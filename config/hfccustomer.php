<?php

namespace Modules\HfcCustomer\Entities;

return [
    'name' => 'HfcBase',
    'link' => 'HfcBase.index',
    'MenuItems' => [
        'Modem Pos System' => [
            'link'	=> 'Mpr.index',
            'icon'	=> 'fa-hdd-o',
            'class' => Mpr::class,
        ],
    ],
];
