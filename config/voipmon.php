<?php

namespace Modules\VoipMon\Entities;

return [
    'name' => 'VoipMon',
    'MenuItems' => [
        'CDRs' => [
            'link'	=> 'Cdr.index',
            'icon'	=> 'fa-address-card-o',
            'class' => Cdr::class,
        ],
    ],
];
