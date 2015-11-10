@extends ('Layout.single')

@section('content_top')

	{{ HTML::linkRoute($route_name.'.index', $view_header) }}

@stop

@section('content_left')

	{{ Form::openDivClass(12) }}
		{{ Form::openDivClass(3) }}
			@if ($create_allowed)

				{{ Form::open(array('route' => $route_name.'.create', 'method' => 'GET')) }}
				{{ Form::submit('Create', ['style' => 'simple']) }}
				{{ Form::close() }}

			@endif
		{{ Form::closeDivClass() }}
	{{ Form::closeDivClass() }}


	{{ Form::openDivClass(12) }}

		{{ Form::open(array('route' => array($route_name.'.destroy', 0), 'method' => 'delete')) }}

		<table>
		@foreach ($view_var as $object)
			<tr>
				<td>
					{{ Form::checkbox('ids['.$object->id.']') }}
				</td>
				<td>
					{{ HTML::linkRoute($route_name.'.edit', $object->get_view_link_title(), $object->id) }}
				</td>
			</tr>
		@endforeach
		</table>

		<br>

		{{ Form::openDivClass(3) }}
			{{ Form::submit('Delete', ['style' => 'simple']) }}
			{{ Form::close() }}

		{{ Form::closeDivClass() }}

	{{ Form::closeDivClass() }}

@stop
