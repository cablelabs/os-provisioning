<?php

namespace Modules\HfcReq\Entities;

return [
    'name' => 'HFC',
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
