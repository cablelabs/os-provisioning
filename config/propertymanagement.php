<?php

namespace Modules\PropertyManagement\Entities;

return [
    'name' => 'PropertyManagement',
    'MenuItems' => [
        'Node' => [
            'link'  => 'Node.index',
            'icon'  => 'fa-share-alt-square',
            'class' => Node::class,
        ],
        'Realty' => [
            'link'  => 'Realty.index',
            'icon'  => 'fa-building-o',
            'class' => Realty::class,
        ],
        'Apartment' => [
            'link'  => 'Apartment.index',
            'icon'  => 'fa-bed',
            'class' => Apartment::class,
        ],
        'Contact' => [
            'link'  => 'Contact.index',
            'icon'  => 'fa-address-card-o',
            'class' => Contact::class,
        ],
    ],
];
