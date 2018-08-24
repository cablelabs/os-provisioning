<?php

namespace Modules\NmsMail\Entities;

return [
    'name' => 'Mail',
    'MenuItems' => [
        'E-Mail' => [
            'link'	=> 'Email.index',
            'icon'	=> 'fa-envelope-o',
            'class' => Email::class,
        ],
    ],
];
