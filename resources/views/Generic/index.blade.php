@extends ('Layout.split84')

@section('content_top')

	{{ HTML::linkRoute($route_name.'.index', $view_header) }}

@stop

@section('content_left')

	{{ Form::openDivClass(12) }}
		<?php
			// searchscope for following form is the current model
			$next_scope = $route_name;
		?>
		{{ Form::openDivClass(6) }}
			{{ Form::model(null, array('route'=>$route_name.'.fulltextSearch', 'method'=>'GET')) }}
				@include('Generic.searchform')
			{{ Form::close() }}
		{{ Form::closeDivClass() }}
	{{ Form::closeDivClass() }}

{{ Form::openDivClass(12) }}
<br>
{{ Form::closeDivClass() }}

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

		@if (isset($query) && isset($scope))
			<h4>Matches for <tt>{{ $query }}</tt> in <tt>{{ $scope }}</tt></h4>
		@endif

		<table>
		@foreach ($view_var as $object)
			<tr>
				<td>
					{{ Form::checkbox('ids['.$object->id.']') }}
				</td>
				<td>

					<?php
						// TODO: move away from view!!
						$cur_model_complete = get_class($object);
						$cur_model_parts = explode('\\', $cur_model_complete);
						$cur_model = array_pop($cur_model_parts);
					?>

				{{ HTML::linkRoute($cur_model.'.edit', $object->get_view_link_title(), $object->id) }}

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
