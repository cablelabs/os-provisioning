@section('content_top_2')

	{{ HTML::linkRoute('Device.edit', 'Edit', array($view_var->id)) }} |
	{{ HTML::linkRoute('Device.controlling_edit', 'Controlling', array($view_var->id)) }} 

@stop
