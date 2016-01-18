@extends ('Layout.split84')

@section('content_top')

	{{ HTML::linkRoute($route_name.'.tree', $view_header) }}

@stop

@section('content_left')

	<!-- Search Field -->
	{{ Form::openDivClass(12) }}
		<?php
			// searchscope for following form is the current model
			$next_scope = $route_name;
		?>
		{{ Form::openDivClass(8) }}
			{{ Form::model(null, array('route'=>$route_name.'.fulltextSearch', 'method'=>'GET')) }}
				@include('Generic.searchform')
			{{ Form::close() }}
		{{ Form::closeDivClass() }}
	{{ Form::closeDivClass() }}

	<!-- new line -->
	{{ Form::openDivClass(12) }}
		<br>
	{{ Form::closeDivClass() }}

	<!-- Create Form -->
	{{ Form::openDivClass(12) }}
		{{ Form::openDivClass(3) }}
			@if ($create_allowed)
				{{ Form::open(array('route' => $route_name.'.create', 'method' => 'GET')) }}
				{{ Form::submit('Create', ['style' => 'simple']) }}
				{{ Form::close() }}
			@endif
		{{ Form::closeDivClass() }}
	{{ Form::closeDivClass() }}

	<!-- database entries inside a form with checkboxes to be able to delete one or more entries -->
	{{ Form::openDivClass(12) }}

		{{ Form::open(array('route' => array($route_name.'.destroy', 0), 'method' => 'delete')) }}

			@if (isset($query) && isset($scope))
				<h4>Matches for <tt>{{ $query }}</tt> in <tt>{{ $scope }}</tt></h4>
			@endif

			<!-- <table> -->

			{{ $view_var }}

			<!-- </table> -->

			<br>

		<!-- delete/submit button of form-->
		{{ Form::openDivClass(3) }}
			{{ Form::submit('Delete', ['style' => 'simple']) }}
			{{ Form::close() }}
		{{ Form::closeDivClass() }}

	{{ Form::closeDivClass() }}

@stop