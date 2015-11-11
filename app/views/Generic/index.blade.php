@extends ('Layout.single')

@section('content_top')

		{{ HTML::linkRoute($model_name.'.index', $view_header) }}

@stop

@section('content_left')

	{{ Form::openDivClass(12) }}
		<?php
			// searchscope for following form is the current model
			$next_scope = $model_name;
		?>
		{{ Form::openDivClass(3) }}
			{{ Form::model(null, array('route'=>$model_name.'.fulltextSearch', 'method'=>'GET')) }}
				@include('Generic.searchform')
			{{ Form::close() }}
		{{ Form::closeDivClass() }}

		{{ Form::openDivClass(3) }}
			@if ($create_allowed)

				{{ Form::open(array('route' => $model_name.'.create', 'method' => 'GET')) }}
				{{ Form::submit('Create', ['style' => 'simple']) }}
				{{ Form::close() }}
			@endif
		{{ Form::closeDivClass() }}
	{{ Form::closeDivClass() }}


	{{ Form::openDivClass(12) }}

		{{ Form::open(array('route' => array($model_name.'.destroy', 0), 'method' => 'delete')) }}

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
