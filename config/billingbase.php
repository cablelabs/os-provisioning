<?php

namespace Modules\BillingBase\Entities;

return [
    'name' => 'BillingBase',
    'link' => 'BillingBase.index',
    'MenuItems' => [
        'Product List' => [
            'link'	=> 'Product.index',
            'icon'	=> 'fa-th-list',
            'class' => Product::class,
        ],
        'SEPA Accounts' => [
            'link'	=> 'SepaAccount.index',
            'icon'	=> 'fa-credit-card',
            'class' => SepaAccount::class,
        ],
        'Settlement Run' => [
            'link'	=> 'SettlementRun.index',
            'icon'	=> 'fa-file-pdf-o',
            'class' => SettlementRun::class,
        ],
        'Cost Center' => [
            'link'	=> 'CostCenter.index',
            'icon'	=> 'fa-creative-commons',
            'class' => CostCenter::class,
        ],
        'Companies' => [
            'link'	=> 'Company.index',
            'icon'	=> 'fa-industry',
            'class' => Company::class,
        ],
        'Salesmen' => [
            'link'	=> 'Salesman.index',
            'icon' => 'fa-vcard',
            'class' => Salesman::class,
        ],
        'Number Range' => [
            'link'	=> 'NumberRange.index',
            'icon' => 'fa-globe',
            'class' => NumberRange::class,
        ],
    ],
];
