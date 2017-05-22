@extends ('Layout.split84')

@section('content_top')

	<li class="active"> {{ HTML::linkRoute($route_name.'.index', $view_header) }} </li>

@stop

@section('content_left')

	<!-- Search Field -->
	@DivOpen(12)
		@DivOpen(8)
			{{ Form::model(null, array('route'=>$route_name.'.fulltextSearch', 'method'=>'GET')) }}
				@include('Generic.searchform')
			{{ Form::close() }}
		@DivClose()
	@DivClose()


	<!-- Create Form -->
	@DivOpen(12)
		@DivOpen(3)
			@if ($create_allowed)
				{{ Form::open(array('route' => $route_name.'.create', 'method' => 'GET')) }}
				{{Form::submit( \App\Http\Controllers\BaseViewController::translate_view('Create '.$b_text, 'Button' ) , ['style' => 'simple']) }}
				{{ Form::close() }}
			@endif
		@DivClose()
	@DivClose()

	<!-- database entries inside a form with checkboxes to be able to delete one or more entries -->
	@DivOpen(12)

		{{ Form::open(array('route' => array($route_name.'.destroy', 0), 'method' => 'delete')) }}

			@if (isset($query) && isset($scope))
				<h4>Matches for <tt>{{ $query }}</tt> in <tt>{{ $scope }}</tt></h4>
			@endif

			<!-- <table> -->
			<br>

			<?php $controller = NameSpaceController::get_controller_name(); ?>
			
			{{ $controller::make_tree_table() }}

			<!-- </table> -->


		<!-- delete/submit button of form-->
		@DivOpen(3)
			{{ Form::submit('Delete', ['!class' => 'btn btn-danger btn-primary m-r-5', 'style' => 'simple']) }}
			{{ Form::close() }}
		@DivClose()

	@DivClose()

@stop



      <!-- <link href="{{asset('components/assets-admin/plugins/jstree/dist/themes/default/style.min.css') }}" rel="stylesheet"> -->
