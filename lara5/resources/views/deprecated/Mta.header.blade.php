@section('content_top')

	{{ HTML::linkRoute($model_name.'.index', $view_header) }} / {{ HTML::linkRoute($model_name.'.edit', $view_var->hostname, array($view_var->id)) }}

@stop
