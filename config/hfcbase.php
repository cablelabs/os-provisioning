<?php

use Modules\HfcReq\Entities\NetElement;

return [
    'name' => 'HfcBase',
    'link' => 'HfcBase.index',
    'MenuItems' => [
        'VicinityGraph' => [
            'link' => 'VicinityGraph.showGraph',
            'icon' => 'fa-sitemap',
            'class' => NetElement::class,
        ],
    ],
];
