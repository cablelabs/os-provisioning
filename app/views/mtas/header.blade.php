@section('content_top')

	{{ HTML::linkRoute('mta.index', 'MTAs') }} / {{ HTML::linkRoute('mta.edit', $mta->hostname, array($mta->id)) }}

@stop
