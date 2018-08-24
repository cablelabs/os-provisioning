<?php

namespace Modules\Ticketsystem\Entities;

return [
    'name' => 'Ticket',
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
