@section('content_top')

	{{ HTML::linkRoute('Modem.index', 'Modems') }} / {{ HTML::linkRoute('Modem.edit', $modem->hostname, array($modem->id)) }}

@stop

@section('content_top_2')

	{{ HTML::linkRoute('Modem.edit', 'Edit', array($modem->id)) }} |
	{{ HTML::linkRoute('Modem.ping', 'Ping', array($modem->id)) }} | 
	{{ HTML::linkRoute('Modem.monitoring', 'Monitoring', array($modem->id)) }} |
	{{ HTML::linkRoute('Modem.log', 'Logging', array($modem->id)) }} |
	{{ HTML::linkRoute('Modem.lease', 'Lease', array($modem->id)) }}
@stop
