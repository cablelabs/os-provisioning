@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute($model_name.'.index', $view_header) }}

@stop

@section('content_left')

	{{ '<h2>'.$view_header.' List</h2>' }}

	{{ Form::open(array('route' => $model_name.'.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}
	
	{{ Form::open(array('route' => array($model_name.'.destroy', 0), 'method' => 'delete')) }}

	@foreach ($view_var as $object)

		<table>
		<tr>
			<td> 
				{{ Form::checkbox('ids['.$object->id.']') }}
				{{ HTML::linkRoute($model_name.'.edit', $object->get_view_link_title(), $object->id) }}
			</td>
		</tr>
		</table>
		
	@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}


@stop