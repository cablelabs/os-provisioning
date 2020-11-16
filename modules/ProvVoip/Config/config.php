<?php

namespace Modules\ProvVoip\Entities;

return [
    'link' => 'ProvVoip.index',
    'MenuItems' => [
        'MTAs' => [
            'link'	=> 'Mta.index',
            'icon'	=> 'fa-fax',
            'class' => Mta::class,
        ],
        'Phonenumbers' => [
            'link'	=> 'Phonenumber.index',
            'icon'	=> 'fa-list-ol',
            'class' => Phonenumber::class,
        ],
        'PhoneTariffs' => [
            'link'	=> 'PhoneTariff.index',
            'icon'	=> 'fa-phone-square',
            'class' => PhoneTariff::class,
        ],
    ],
];
