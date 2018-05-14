<h4>{{ \App\Http\Controllers\BaseViewController::translate_view('Tickets', 'Dashboard') }}</h4>
<p>
    @if ($data['tickets']['total'])
        {{ $data['tickets']['total'] }}
    @else
        {{ \App\Http\Controllers\BaseViewController::translate_view('NoTickets', 'Dashboard') }}
    @endif
</p>
