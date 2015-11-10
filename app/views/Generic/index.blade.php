@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute($model_name.'.index', $view_header) }}

@stop

@section('content_left')

	{{ '<h2>'.$view_header.' List</h2>' }}

	<?php
		// searchscope for following form is the current model
		$scope = $model_name;
	?>
	{{ Form::model(null, array('route'=>$model_name.'.fulltextSearch', 'method'=>'GET')) }}
		@include('Generic.searchform')
	{{ Form::close() }}

	@if ($create_allowed)

		{{ Form::open(array('route' => $model_name.'.create', 'method' => 'GET')) }}
		{{ Form::submit('Create') }}
		{{ Form::close() }}

	@endif

	{{ Form::open(array('route' => array($model_name.'.destroy', 0), 'method' => 'delete')) }}

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

	{{ Form::submit('Delete') }}
	{{ Form::close() }}


@stop
