<?php

namespace Modules\NmsMail\Entities;

return [
    'MenuItems' => [
        'E-Mail' => [
            'link'	=> 'Email.index',
            'icon'	=> 'fa-envelope-o',
            'class' => Email::class,
        ],
    ],
];
