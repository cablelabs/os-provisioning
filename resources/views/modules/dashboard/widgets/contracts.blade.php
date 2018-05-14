<h4>{{ \App\Http\Controllers\BaseViewController::translate_view('Contracts', 'Dashboard') }} {{ date('m/Y') }}</h4>
<p>
    @if ($data['contracts']['total'])
        {{ $data['contracts']['total'] }}
    @else
        {{ \App\Http\Controllers\BaseViewController::translate_view('NoContracts', 'Dashboard') }}
    @endif
</p>
