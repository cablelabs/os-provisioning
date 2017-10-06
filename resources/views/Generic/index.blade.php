{{--

@param $route_name: the base route name of this object class
@param $headline: 	the link header description in HTML
@param $create_allowd: create button allowed?
@param $model: 	model object to call functions
@param $view_var: 	array() of objects to be displayed

@param $query:
@param $scope:

--}}

@extends ('Layout.split84-nopanel')

@section('content_top')
	<li class="active">
		<a href="{{route($route_name.'.index')}}">
		@if (isset($view_var) && is_array($view_var))
		{{ \App\Http\Controllers\BaseViewController::__get_view_icon($view_var[0]).' '}}
		@else
		{{ $model->view_icon().' '}}
		@endif
		{{$headline}}
		</a>
	</li>
@stop

@section('content_left')

	<!-- Headline: means icon followed by headline -->
	@DivOpen(12)
		<h1 class="page-header">
		@if (isset($view_var) && is_array($view_var) )
		{{ \App\Http\Controllers\BaseViewController::__get_view_icon($view_var[0]).' '}}
		@else
		{{ $model->view_icon().' '}}
		@endif
		{{$headline}}
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
		<?php // init DataTable ?>
			<table class="table table-hover datatable table-bordered" id="datatable">
		<?php // Get Headerdata and translate with translation files ?>
		@if (isset($model) && isset($view_var) && method_exists( BaseController::get_model_obj() , 'view_index_label_ajax' ))
				<thead>
					<tr>
						<th width="30px"></th>
						@if (isset($delete_allowed) && $delete_allowed == true)
							<th witdth="30px" id="selectall" style="text-align:center; vertical-align:middle;">
								<input id ="allCheck" data-trigger="hover" style='simple' type='checkbox' value='1' data-container="body" data-toggle="tooltip" data-placement="top"
								data-delay='{"show":"350"}' data-original-title="{{\App\Http\Controllers\BaseViewController::translate_label('Select All')}}">
							</th>
						@endif
						@if (isset($model) && is_array($model->view_index_label_ajax()) && isset($model->view_index_label_ajax()['index_header']))
							@foreach ($model->view_index_label_ajax()['index_header'] as $field)
								<th style="text-align:center; vertical-align:middle;">{{ trans('dt_header.'.$field).' ' }}
								@if ((!empty($model->view_index_label_ajax()['sortsearch'])) && ($model->view_index_label_ajax()['sortsearch'] == [$field => 'false']))
									<i class="fa fa-info-circle text-info" data-trigger="hover" data-container="body" data-toggle="tooltip" data-placement="top" data-delay='{"show":"250"}'
									data-original-title="{{\App\Http\Controllers\BaseViewController::translate_label('You cant sort or search this Column')}}"></i>
								@endif
								</th>
							@endforeach
						@endif
					</tr>
				</thead>
		@endif
		<?php // Generate AJAX Datatable Footer ?>
		@if (isset($model) && isset($view_var) && isset($index_datatables_ajax_enabled) && method_exists( BaseController::get_model_obj() , 'view_index_label_ajax' ))
				<tfoot>
					<tr>
						<th></th>
						@if (isset($delete_allowed) && $delete_allowed == true)
							<th></th>
						@endif
						@foreach ($model->view_index_label_ajax()['index_header'] as $field)
							@if ((!empty($model->view_index_label_ajax()['sortsearch'])) && ( array_has( $model->view_index_label_ajax()['sortsearch'] , $field) ) )
								<th></th>
							@else
								<th class="searchable"></th>
							@endif
						@endforeach
					</tr>
				</tfoot>
			</table>
		<?php // For Backwards compatibility: Generate the Datatable the old way ?>
		@elseif (method_exists( BaseController::get_model_obj() , 'view_index_label' ) && isset($view_var[0]) )
			@if (!method_exists( BaseController::get_model_obj() , 'view_index_label_ajax' ))
				<thead>
					<tr>
						<th width="30px"></th>
						@if (isset($delete_allowed) && $delete_allowed == true)
							<th id="selectall" style="text-align:center; vertical-align:middle;">
								<input id ="allCheck" data-trigger="hover" style='simple' type='checkbox' value='1' data-container="body" data-toggle="tooltip" data-placement="top"
								data-delay='{"show":"350"}' data-original-title="{{\App\Http\Controllers\BaseViewController::translate_label('Select All')}}">
							</th>
						@endif
						<!-- Parse view_index_label() header_index  -->
						@if (isset($view_var[0]) && is_array($view_var[0]->view_index_label()) && isset($view_var[0]->view_index_label()['index_header']))
							@foreach ($view_var[0]->view_index_label()['index_header'] as $field)
								<th> {{ \App\Http\Controllers\BaseViewController::translate_label($field) }} </th>
							@endforeach
						@endif
					</tr>
				</thead>
			@endif
				<!-- Index Table Entries -->
				<tbody>
				@foreach ($view_var as $object)
					<tr class="{{\App\Http\Controllers\BaseViewController::prep_index_entries_color($object)}}">
							<td width="30"></td>
						@if (isset($delete_allowed) && $delete_allowed == true)
							<td width="30" align="center"> {{ Form::checkbox('ids['.$object->id.']', 1, null, null, ['style' => 'simple', 'disabled' => $object->index_delete_disabled ? 'disabled' : null]) }} </td>
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
			</table>
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
