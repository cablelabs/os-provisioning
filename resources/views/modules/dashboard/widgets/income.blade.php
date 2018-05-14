<h4>{{ \App\Http\Controllers\BaseViewController::translate_view('Net Income', 'Dashboard') }} {{ date('m/Y') }}</h4>
<p>
    @if ($data['income']['total'])
        {{ number_format($data['income']['total'], 0, ',', '.') }}
    @else
        {{ number_format(0, 0, ',', '.') }}
    @endif
</p>
