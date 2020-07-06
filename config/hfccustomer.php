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

    /*
    |--------------------------------------------------------------------------
    | Modem Monitoring Threshholds
    |--------------------------------------------------------------------------
    |
    | To indicate whether a modem or a group of modems is in a warning or
    | a critical state threshhold values need to be specified. To ensure
    | an easy start NMS Prime already provides default values for you.
    |
    */
    'threshhold' => [
        'single' => [
            'us' => [
                'warning' => 50,
                'critical' => 55,
            ],
        ],
        'avg' => [
            'us' => [
                'warning' => 45,
                'critical' => 52,
            ],
            'percentage' => [
                'warning' => 70,
                'critical' => 50,
                'multipleClusters' => 75,
            ],
        ],
    ],
];
