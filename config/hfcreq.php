<?php

namespace Modules\HfcReq\Entities;

return [
    'name' => 'HfcBase',
    'link' => 'HfcBase.index',
    'parent' => 'HfcBase',
    'MenuItems' => [
        'Net Element Types' => [
            'link'	=> 'NetElementType.index',
            'icon'	=> 'fa-object-group',
            'class' => NetElementType::class,
        ],
        'Net Elements' => [
            'link'	=> 'NetElement.index',
            'icon'	=> 'fa-object-ungroup',
            'class' => NetElement::class,
        ],
    ],
];
