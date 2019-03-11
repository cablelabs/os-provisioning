{{--

@param $headline: the link header description in HTML

@param $view_var: the object we are editing
@param $form_update: the update route which should be called when clicking save
@param $form_path: the form view to be displayed inside this blade (mostly Generic.form)
@param $tabs: the page hyperlinks returned from analysisPage() or prep_right_panels()
@param $relations: the relations array() returned by prep_right_panels() in BaseViewController

--}}
@extends ('Layout.split-nopanel')

@section('content_top')

	{!! $headline !!}

@stop


@section('content_left')
	@include ('Generic.logging')
	<?php
		$blade_type = 'relations';
	?>

	@include('Generic.above_infos')
	{!! Form::model($view_var, ['route' => [$form_update, $view_var->id], 'method' => 'put', 'files' => true, 'id' => 'EditForm']) !!}

		@include($form_path, $view_var)

	{{ Form::close() }}

@stop


<?php $api = App\Http\Controllers\BaseViewController::get_view_has_many_api_version($relations) ?>

@section('content_right')
@if(isset($relations) && !empty($relations))
<div class="col-md-{{isset($edit_right_md_size) ? $edit_right_md_size : 4}} ui-sortable">
	@foreach($relations as $view => $relation)

	<?php if (!isset($i)) $i = 0; else $i++; ?>

	{{-- The section content for the new Panel --}}
	@section("content_$i")

			{{-- old API: directly load relation view. NOTE: old API new class var is $view --}}
			@if ($api == 1)
				@include('Generic.relation', [$relation, 'class' => $view, 'key' => strtolower($view_var->table).'_id'])
			@endif

			{{-- new API: parse data --}}
			@if ($api == 2)
				@if (is_array($relation))

					{{-- include pure HTML --}}
					@if (isset($relation['html']))
						{!! $relation['html'] !!}
					@endif

					{{-- include a view --}}
					@if (isset($relation['view']))
						@if (is_string($relation['view']))
							@include ($relation['view'])
						@endif
						@if (is_array($relation['view']))
							@include ($relation['view']['view'], isset($relation['view']['vars']) ? $relation['view']['vars'] : [])
							<?php $md_size = isset($relation['view']['vars']['md_size']) ? $relation['view']['vars']['md_size'] : null; ?>
						@endif
					@endif

					{{-- include a relational class/object/table, like Contract->Modem --}}
					@if (isset($relation['class']) && isset($relation['relation']))
						@include('Generic.relation', ['relation' => $relation['relation'],
													  'class' => $relation['class'],
													  'key' => strtolower($view_var->table).'_id',
													  'options' => isset($relation['options']) ? ($relation['options']) : null])
					@endif

				@endif
			@endif

		@stop

		{{-- The Bootstap Panel to include --}}
		@include ('bootstrap.panel', [
			'content' => "content_$i",
			'view_header' => trans('view.Header_Assigned').' '.\App\Http\Controllers\BaseViewController::translate_view($view, 'Header' , 2),
			'md' => 12,
			'options' => $relation['panelOptions'] ?? null,
			])
			{{-- 'md' => isset($md_size) ? $md_size : (isset($edit_right_md_size) ? $edit_right_md_size : 4))) --}}

	@endforeach
	</div>


@endif

	{{-- Alert --}}
	@if (Session::has('alert'))
		@foreach (Session::get('alert') as $notif => $message)
			@include('bootstrap.alert', array('message' => $message, 'color' => $notif))
			<?php Session::forget("alert.$notif"); ?>
		@endforeach
	@endif

@stop

@section('javascript')
@if(isset($tabs))
<script language="javascript">
	$('#loggingtab').click(function() {
		$('.tab-content').toggle();
	});

	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var table = $('table.datatable').DataTable(
		{
		{{-- STANDARD CONFIGURATION --}}
			{{-- Translate Datatables Base --}}
				@include('datatables.lang')
			{{-- Buttons above Datatable for export, print and change Column Visibility --}}
            	@include('datatables.buttons')
        	{{-- Show Pagination only when the results do not fit on one page --}}
            	@include('datatables.paginate')
			retrieve: true,
			responsive: {
				details: {
				type: 'column', {{-- auto resize the Table to fit the viewing device --}}
				}
			},
			dom: "Btip",
			fnAdjustColumnSizing: true,
			autoWidth: false,
			aoColumnDefs: [ {
				className: 'control',
				orderable: false,
				targets:   [0]
			},
			{
                "targets": [ 4 , 5 ],
                "visible": false,
            },
			],
		{{-- AJAX CONFIGURATION --}}
			processing: true,
			serverSide: true,
			deferRender: true,
			ajax: '{!! Route( App\Http\Controllers\NamespaceController::get_route_name(). ".guilog", $view_var) !!}',
			columns:[
						{data: 'responsive', orderable: false, searchable: false},
						{data: 'created_at', name: 'created_at'},
						{data: 'username', name: 'username'},
						{data: 'method', name: 'method'},
						{data: 'model', name: 'model'},
						{data: 'model_id', name: 'model_id'},
			],
		});
	$( $.fn.dataTable.tables(true) ).DataTable().responsive.recalc();
	});

	$(document).ready(function() {
		@foreach (['street', 'zip', 'city', 'district'] as $element)
			$('{{'#'.$element}}').autocomplete({
				source:function (data, response) {
					$.ajax({
						url:'/admin/Contract/autocomplete/{!! $element !!}?q=' + $('#{!! $element !!}').val(),
						success:function(data){
							response(data);
						}
					})
				}
			});
		@endforeach
	});
</script>
@include('Generic.handlePanel')
@endif
@stop
