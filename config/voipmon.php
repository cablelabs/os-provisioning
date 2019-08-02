<?php

namespace Modules\VoipMon\Entities;

return [
    'name' => 'VoIP',
    'MenuItems' => [
        'CDRs' => [
            'link'	=> 'Cdr.index',
            'icon'	=> 'fa-address-card-o',
            'class' => Cdr::class,
        ],
    ],
];
