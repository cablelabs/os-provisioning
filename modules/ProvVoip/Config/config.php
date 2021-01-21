<?php

namespace Modules\ProvVoip\Entities;

return [
    'link' => 'ProvVoip.index',
    'MenuItems' => [
        'MTA' => [
            'link'	=> 'Mta.index',
            'icon'	=> 'fa-fax',
            'class' => Mta::class,
        ],
        'Phonenumber' => [
            'link'	=> 'Phonenumber.index',
            'icon'	=> 'fa-list-ol',
            'class' => Phonenumber::class,
        ],
        'PhoneTariff' => [
            'link'	=> 'PhoneTariff.index',
            'icon'	=> 'fa-phone-square',
            'class' => PhoneTariff::class,
        ],
    ],
];
