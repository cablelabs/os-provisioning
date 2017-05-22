{{--

@param $route_name: the base route name of this object class
@param $headline: 	the link header description in HTML
@param $create_allowd: create button allowed?
@param $view_var: 	array() of objects to be displayed

@param $query:
@param $scope:

--}}

@extends ('Layout.split84-nopanel')

@section('content_top')

	<li class="active">{{ HTML::linkRoute($route_name.'.index', $headline) }}</li>

@stop

@section('content_left')

	<!-- Headline: means icon followed by headline -->_
	@DivOpen(12)
		<h1 class="page-header">
		{{\App\Http\Controllers\BaseViewController::__get_view_icon(isset($view_var[0]) ? $view_var[0] : null).' '}}
		<?php
		if (isset($view_var[0]))
			echo $view_var[0]->view_headline();
		else
		{
			// handle empty tables ..
			// TODO: make me smarter :)
			$class = \App\Http\Controllers\NamespaceController::get_model_name();
			echo $class::view_headline();
		}
		?>
		</h1>
	@DivClose()

	<!-- Create Form -->
	@DivOpen(12)
			@if ($create_allowed)
				{{ Form::open(array('route' => $route_name.'.create', 'method' => 'GET')) }}
				<button class="btn btn-primary m-b-15" style="simple">
					<i class="fa fa-plus fa-lg m-r-10" aria-hidden="true"></i>
					{{ \App\Http\Controllers\BaseViewController::translate_view('Create '.$b_text, 'Button' )}}
				</button>
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

	{{-- man can use the session key “tmp_info_above_index_list” to show additional data above the form for one screen --}}
	{{-- simply use Session::push('tmp_info_above_index_list', 'your additional data') in your observers or where you want --}}
	@if (Session::has('tmp_info_above_index_list'))
		@DivOpen(12)
		<?php
			$tmp_info_above_index_list = Session::get('tmp_info_above_index_list');

			// for better handling: transform strings to array (containing one element)
			if (is_string($tmp_info_above_index_list)) {
				$tmp_info_above_index_list = [$tmp_info_above_index_list];
			};
		?>
		@foreach($tmp_info_above_index_list as $info)
			<div style="font-weight: bold; padding-top: 0px; padding-left: 10px; margin-bottom: 5px; border-left: solid 2px #ffaaaa">
				{{ $info }}
			</div>
		@endforeach
		<br>
		<?php
			// as this shall not be shown on later screens: remove from session
			// we could use Session::flash for this behavior – but this supports no arrays…
			Session::forget('tmp_info_above_index_list'); ?>
		@DivClose()
	@endif

	<!-- database entries inside a form with checkboxes to be able to delete one or more entries -->
	@DivOpen(12)

		{{ Form::open(array('route' => array($route_name.'.destroy', 0), 'method' => 'delete')) }}

			@if (isset($query) && isset($scope))
				<h4><?php echo trans('view.Search_MatchesFor'); ?><tt>'{{ $query }}'</tt> <?php echo trans('view.Search_In') ?>
				<tt>{{ \App\Http\Controllers\BaseViewController::translate_view($view_header, 'Header', 1) }}</tt></h4>
			@endif

		@if (isset($view_var[0]))
			<table class="table table-hover table-striped datatable table-striped table-bordered collapsed">
				<!-- TODO: add concept to parse header fields for index table - like firstname, lastname, ..-->
				<thead>
					<tr>
						<th></th>
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
				<tbody>
				@foreach ($view_var as $object)
					<tr class="{{\App\Http\Controllers\BaseViewController::prep_index_entries_color($object)}}">
							<td width="30"></td>
						@if ($delete_allowed)
							<td width="30" align="center"> {{ Form::checkbox('ids['.$object->id.']', 1, null, null, ['style' => 'simple', 'disabled' => $object->index_delete_disabled ? 'disabled' : null]) }} </td>
						@else
							<td/>
						@endif

						<!-- Parse view_index_label()  -->
						<?php $i = 0; // display link only on first element ?>
						@foreach (is_array($object->view_index_label()) ? $object->view_index_label()['index'] : [$object->view_index_label()] as $field)
							<td class="ClickableTd">
								@if ($i++ == 0)
									{{$object->view_icon()}}
									<strong>{{ HTML::linkRoute($route_name.'.edit', $field, $object->id) }}</strong>
								@else
									{{ $field }}
								@endif
							</td>
						@endforeach
					</tr>
				@endforeach
				</tbody>
			</table>
		@else
			<h4>{{ $view_no_entries }}</h4>
		@endif
	@DivClose()

	@DivOpen(12)
		<!-- delete/submit button of form -->
		@if ($delete_allowed)
			<button class="btn btn-danger btn-primary m-r-5 m-t-15" style="simple">
					<i class="fa fa-trash-o fa-lg m-r-10" aria-hidden="true"></i>
					{{ \App\Http\Controllers\BaseViewController::translate_view('Delete', 'Button' ) }}
			</button>
			{{ Form::close() }}
		@endif
		<!-- only show page buttons if we actually use pagination -->
		@if ($view_var instanceof \Illuminate\Pagination\Paginator)
			<span class="pull-right">{{ $view_var->links() }}</span>
		@endif
	@DivClose()

@stop
