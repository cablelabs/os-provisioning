<?php

namespace Modules\ProvVoip\Entities;

$conf = [
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
    ],
];

if (\Module::collections()->has('BillingBase')) {
    $conf['PhoneTariff'] = [
        'link'	=> 'PhoneTariff.index',
        'icon'	=> 'fa-phone-square',
        'class' => PhoneTariff::class,
    ];
}

return $conf;
