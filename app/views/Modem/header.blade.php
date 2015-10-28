@section('content_top')

	{{ HTML::linkRoute($model_name.'.index', $view_header) }} / {{ HTML::linkRoute($model_name.'.edit', $view_var->hostname, array($view_var->id)) }}

@stop

@section('content_top_2')

	{{ HTML::linkRoute('Modem.edit', 'Edit', array($view_var->id)) }} |
	{{ HTML::linkRoute('Modem.ping', 'Ping', array($view_var->id)) }} | 
	{{ HTML::linkRoute('Modem.monitoring', 'Monitoring', array($view_var->id)) }} |
	{{ HTML::linkRoute('Modem.log', 'Logging', array($view_var->id)) }} |
	{{ HTML::linkRoute('Modem.lease', 'Lease', array($view_var->id)) }}
@stop
