{{--

@param $route_name: the base route name of this object class
@param $headline: 	the link header description in HTML
@param $create_allowd: create button allowed?
@param $view_var: 	array() of objects to be displayed

@param $query:
@param $scope:

--}}

@extends ('Layout.split84')

@section('content_top')

	{{ HTML::linkRoute($route_name.'.index', $headline) }}

@stop

@section('content_left')

	<!-- Create Form -->
	@DivOpen(12)
			@if ($create_allowed)
				{{ Form::open(array('route' => $route_name.'.create', 'method' => 'GET')) }}
				{{ Form::submit( \App\Http\Controllers\BaseViewController::translate_view('Create '.$b_text, 'Button' ) , ['!class' => 'btn btn-primary m-b-15','style' => 'simple']) }}
				{{ Form::close() }}
			@endif
	@DivClose()

	<!-- Search TEMPORARY DISABLED
	@DivOpen(3)
			{{ Form::model(null, array('route'=>$route_name.'.fulltextSearch', 'method'=>'GET'), 'simple') }}
				@include('Generic.searchform')
			{{ Form::close() }}
	@DivClose()
	-->

	<!-- database entries inside a form with checkboxes to be able to delete one or more entries -->
	@DivOpen(12)

		{{ Form::open(array('route' => array($route_name.'.destroy', 0), 'method' => 'delete')) }}

			@if (isset($query) && isset($scope))
				<h4><?php echo trans('view.Search_MatchesFor'); ?><tt>'{{ $query }}'</tt> <?php echo trans('view.Search_In') ?> 
				<tt>{{ \App\Http\Controllers\BaseViewController::translate_view($view_header, 'Header', 1) }}</tt></h4>
			@endif

		@if (isset($view_var[0]))
			<table class="table table-hover table-striped datatable nowrap table-striped table-bordered collapsed">
	

				<!-- TODO: add concept to parse header fields for index table - like firstname, lastname, ..-->
				<thead>
					<tr role="row">
						<th></th>
						<!-- Parse view_index_label() header_index  -->
						@if (isset($view_var[0]) && is_array($view_var[0]->view_index_label()) && isset($view_var[0]->view_index_label()['index_header']))
							@foreach ($view_var[0]->view_index_label()['index_header'] as $field)
								<th> {{ \App\Http\Controllers\BaseViewController::translate_label($field) }} </th>
							@endforeach
						@endif
					</tr>
				</thead>

				<!-- Index Table Entries -->
				@foreach ($view_var as $object)
					<tr class="{{\App\Http\Controllers\BaseViewController::prep_index_entries_color($object)}}">

						@if ($delete_allowed)
							<td width=50> {{ Form::checkbox('ids['.$object->id.']', 1, null, null, ['style' => 'simple', 'disabled' => $object->index_delete_disabled ? 'disabled' : null]) }} </td>
						@else
							<td/>
						@endif

						<!-- Parse view_index_label()  -->
						<?php $i = 0; // display link only on first element ?>
						@foreach (is_array($object->view_index_label()) ? $object->view_index_label()['index'] : [$object->view_index_label()] as $field)
							<td class="ClickableTd">
								@if ($i++ == 0)
									{{ HTML::linkRoute($route_name.'.edit', $field, $object->id) }}
								@else
									{{ $field }}
								@endif
							</td>
						@endforeach
					</tr>
				@endforeach

			</table>
		@else
			<h4>{{ $view_no_entries }}</h4>
		@endif
	@DivClose()

	@DivOpen(12)
		<!-- delete/submit button of form -->
		@if ($delete_allowed)
			{{ Form::submit( \App\Http\Controllers\BaseViewController::translate_view('Delete', 'Button' ), ['!class' => 'btn btn-danger btn-primary m-r-5 m-t-15', 'style' => 'simple']) }}
			{{ Form::close() }}
		@endif
		<!-- only show page buttons if we actually use pagination -->
		@if ($view_var instanceof \Illuminate\Pagination\Paginator)
			<span class="pull-right">{{ $view_var->links() }}</span>
		@endif
	@DivClose()

@stop
