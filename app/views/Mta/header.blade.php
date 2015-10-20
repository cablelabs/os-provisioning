@section('content_top')

	{{ HTML::linkRoute('Mta.index', 'MTAs') }} / {{ HTML::linkRoute('Mta.edit', $mta->hostname, array($mta->id)) }}

@stop
