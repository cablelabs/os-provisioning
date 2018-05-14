Hallo {{ $user->first_name }},

Dir wurde ein neues {{ link_to_route('Ticket.edit', 'Ticket', ['id' => $ticket->id]) }} zugewiesen.

Ticket ID: {{ $ticket->id }} <br />
Titel: {{ $ticket->name }}