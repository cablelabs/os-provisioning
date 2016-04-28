{{--

@param $route_name: the base route name of this object class
@param $headline: the link header description in HTML
@param $create_allowd: create button allowed?
@param $view_var: array() of objects to be displayed

@param $query:
@param $scope:

--}}

@extends ('Layout.split84')

@section('content_top')

	{{ HTML::linkRoute($route_name.'.index', $headline) }}

@stop

@section('content_left')

	<!-- Search Field -->
	@DivOpen(12)
		@DivOpen(6)
			{{ Form::model(null, array('route'=>$route_name.'.fulltextSearch', 'method'=>'GET')) }}
				@include('Generic.searchform')
			{{ Form::close() }}
		@DivClose()
	@DivClose()

	@DivOpen(12)
		<br>
	@DivClose()

	<!-- Create Form -->
	@DivOpen(12)
		@DivOpen(3)
			@if ($create_allowed)
				{{ Form::open(array('route' => $route_name.'.create', 'method' => 'GET')) }}
				{{ Form::submit('Create', ['style' => 'simple']) }}
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

			<table class="table">

				<!-- TODO: add concept to parse header fields for index table - like firstname, lastname, ..-->
				<thead>
					<tr>
						<td></td>
						<!-- Parse view_index_label() header_index  -->
						@if (isset($view_var[0]) && is_array($view_var[0]->view_index_label()) && isset($view_var[0]->view_index_label()['index_header']))
							@foreach ($view_var[0]->view_index_label()['index_header'] as $field)
								<td> {{ \App\Http\Controllers\BaseViewController::translate($field) }} </td>
							@endforeach
						@endif
					</tr>
				</thead>

				<!-- Index Table Entries -->
				@foreach ($view_var as $object)
					<tr class={{\App\Http\Controllers\BaseViewController::prep_index_entries_color($object)}}>
						<td width=50> {{ Form::checkbox('ids['.$object->id.']', 1, null, null, ['style' => 'simple']) }} </td>

						<!-- Parse view_index_label()  -->
						<?php $i = 0; // display link only on first element ?>
						@foreach (is_array($object->view_index_label()) ? $object->view_index_label()['index'] : [$object->view_index_label()] as $field)
							@if ($i++ == 0)
								<td> {{ HTML::linkRoute($route_name.'.edit', $field, $object->id) }} </td>
							@else
								<td> {{ $field }} </td>
							@endif
						@endforeach
					</tr>
				@endforeach

			</table>

			<br>

		<!-- delete/submit button of form-->
		@DivOpen(3)
			{{ Form::submit('Delete', ['style' => 'simple']) }}
			{{ Form::close() }}
		@DivClose()

	@DivClose()

@stop
