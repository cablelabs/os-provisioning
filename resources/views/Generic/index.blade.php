{{--

@param $route_name: the base route name of this object class
@param $headline: 	the link header description in HTML
@param $create_allowd: create button allowed?
@param $model: 	model object to call functions
@param $view_var: 	array() of objects to be displayed

@param $query:
@param $scope:

--}}

<?php
	$blade_type = 'index_list';
?>

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

	@include('Generic.above_infos')

	<!-- database entries inside a form with checkboxes to be able to delete one or more entries -->
	@DivOpen(12)
		{{ Form::open(array('route' => array($route_name.'.destroy', 0), 'method' => 'delete')) }}
		{{-- init DataTable --}}
		<table class="table table-hover datatable table-bordered" id="datatable">
			{{-- Get Headerdata and translate with translation files --}}
			<thead>
				<tr>
					<th class="nocolvis" width="30px"></th> {{-- Responsive Column --}}
					@if (isset($delete_allowed) && $delete_allowed == true) {{-- Checkbox Column if delete is allowed --}}
						<th class="nocolvis" witdth="30px" id="selectall" style="text-align:center; vertical-align:middle;">
							<input id ="allCheck" data-trigger="hover" style='simple' type='checkbox' value='1' data-container="body" data-toggle="tooltip" data-placement="top"
							data-delay='{"show":"350"}' data-original-title="{{\App\Http\Controllers\BaseViewController::translate_label('Select All')}}">
						</th>
					@endif
					{{-- Get Header if possible with new Format - for Backwards compatibility old one stays --}}
					@if (isset($model) && method_exists( BaseController::get_model_obj() , 'view_index_label_ajax' ) && is_array($model->view_index_label_ajax()) && isset($model->view_index_label_ajax()['index_header']))
						@foreach ($model->view_index_label_ajax()['index_header'] as $field)
							<th class="content" style="text-align:center; vertical-align:middle;">{{ trans('dt_header.'.$field).' ' }}
							@if ((!empty($model->view_index_label_ajax()['sortsearch'])) && ($model->view_index_label_ajax()['sortsearch'] == [$field => 'false']))
								<i class="fa fa-info-circle text-info" data-trigger="hover" data-container="body" data-toggle="tooltip" data-placement="top" data-delay='{"show":"250"}'
								data-original-title="{{trans('helper.SortSearchColumn')}}"></i>
							@endif
							</th>
						@endforeach
					@elseif (isset($view_var[0]) && is_array($view_var[0]->view_index_label()) && isset($view_var[0]->view_index_label()['index_header']) )
							@foreach ($view_var[0]->view_index_label()['index_header'] as $field)
								<th class="content"> {{ \App\Http\Controllers\BaseViewController::translate_label($field) }} </th>
							@endforeach
					@endif
				</tr>
			</thead>
			<tbody>
			{{-- For Backwards compatibility: Generate the Datatable the old way --}}
			@if (method_exists( BaseController::get_model_obj() , 'view_index_label' ) && isset($view_var[0]))
				@foreach ($view_var as $object)
					<tr class="{{\App\Http\Controllers\BaseViewController::prep_index_entries_color($object)}}">
							<td width="30"></td>
						@if (isset($delete_allowed) && $delete_allowed == true)
							<td width="30" align="center"> {{ Form::checkbox('ids['.$object->id.']', 1, null, null, ['style' => 'simple', 'disabled' => $object->index_delete_disabled ? 'disabled' : null]) }} </td>
						@endif
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
			@endif
			</tbody>
			<tfoot>
			@if (isset($model) && isset($view_var) && method_exists( BaseController::get_model_obj() , 'view_index_label_ajax' ))
				<tr>
					<th></th>  {{-- Responsive Column --}}
					@if (isset($delete_allowed) && $delete_allowed == true)
						<th></th> {{-- Checkbox Column if delete is allowed --}}
					@endif
					@foreach ($model->view_index_label_ajax()['index_header'] as $field)
						@if ((!empty($model->view_index_label_ajax()['sortsearch'])) && ( array_has( $model->view_index_label_ajax()['sortsearch'] , $field) ) )
							<th></th>
						@else
							<th class="searchable"></th>
						@endif
					@endforeach
				</tr>
			@endif
			</tfoot>
		</table>
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
