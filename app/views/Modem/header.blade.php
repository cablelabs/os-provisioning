@section('content_top')

	{{ HTML::linkRoute('modem.index', 'Modems') }} / {{ HTML::linkRoute('modem.edit', $modem->hostname, array($modem->id)) }}

@stop

@section('content_top_2')

	{{ HTML::linkRoute('modem.edit', 'Edit', array($modem->id)) }} |
	{{ HTML::linkRoute('modem.ping', 'Ping', array($modem->id)) }} | 
	{{ HTML::linkRoute('modem.monitoring', 'Monitoring', array($modem->id)) }} |
	{{ HTML::linkRoute('modem.log', 'Logging', array($modem->id)) }} |
	{{ HTML::linkRoute('modem.lease', 'Lease', array($modem->id)) }}
@stop