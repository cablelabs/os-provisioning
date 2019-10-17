<?php

namespace Modules\OverdueDebts\Entities;

return [
    'name' => 'OverdueDebts',
    // Change the type by inserting DEBT_MGMT_TYPE=csv in /etc/nmsprime/env/overduedebts.php
    'debtMgmtType' => env('DEBT_MGMT_TYPE', 'sta'),
    'MenuItems' => [
        'Debt' => [
            'link'	=> 'Debt.result',
            'icon'	=> 'fa-usd',
            'class' => Debt::class,
        ],
    ],
];
