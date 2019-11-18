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
        // Realties and Apartments where signal needs to be cut off caused by e.g. contract cancelation
        'CutoffList' => [
            'link'  => 'CutoffList.index',
            // 'link'  => 'Realty.cutoff',
            // 'icon'  => 'fa-bolt',
            'icon'  => 'fa fa-chain-broken',
            // 'icon'  => 'fa fa-fw fa-chain-broken',
            // 'icon'  => 'fa-scissors',
            'class' => Realty::class,
        ],
    ],
];
