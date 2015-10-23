@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute($class_name'.index', $gui_name) }}
		{{ HTML::linkRoute('Cmts.index', 'CMTS') }}

@stop


@section('content_left')

	<h2>$gui_name List</h2>

	{{ Form::open(array('route' => $class_name'.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}
	
	{{ Form::open(array('route' => array($class_name'.destroy', 0), 'method' => 'delete')) }}

		@foreach ($object_var as $key => $var)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$var->id.']') }}
						{{ HTML::linkRoute($class_name'.edit', $edit_link[$key], $var->id) }}
					</td>
				</tr>
				</table>
			
		@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}


@stop