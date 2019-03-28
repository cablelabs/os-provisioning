<?php

namespace Modules\Ticketsystem\Entities;

return [
    'name' => 'Ticket',
    'link' => 'Ticket.dashboard',
    'MenuItems' => [
        'TicketTypes' => [
            'link' => 'TicketType.index',
            'icon'	=> 'fa-ticket',
            'class' => TicketType::class,
        ],
    'Tickets' => [
      'link' => 'Ticket.index',
            'icon'	=> 'fa-ticket',
            'class' => Ticket::class,
        ],
    ],
];
