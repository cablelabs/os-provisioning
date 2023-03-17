<?php

namespace Modules\Ticketsystem\Helper;

use Illuminate\Database\Eloquent\Collection;
use Modules\Ticketsystem\Entities\Ticket;

class CustomTicketCollection extends Collection
{
    /**
     * Calculate a severity indicator over all tickets. Tends against 0 for in
     * process tickets, 0.5 for new tickets and is 1 if there are no tickets
     * to count.
     *
     * @return int
     */
    public function normalizedSeverity()
    {
        $count = $this->count();

        $severity = $this->reduce(function ($carry, $ticket) {
            if ($ticket->state === Ticket::STATES['New']) {
                return $carry + 0.5;
            }

            return $carry + 1;
        });

        return $count ? 1 - ($severity / $count) : 1;
    }
}
