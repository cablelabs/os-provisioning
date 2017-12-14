{{--

@param $headline: the link header description in HTML

@param $view_var: the object we are editing
@param $form_update: the update route which should be called when clicking save
@param $form_path: the form view to be displayed inside this blade (mostly Generic.edit)
@param $panel_right: the page hyperlinks returned from prepare_tabs() or prep_right_panels()
@param $relations: the relations array() returned by prep_right_panels() in BaseViewController

--}}
@extends ('Layout.split-nopanel')

@section('content_top')

	{{ $headline }}

@stop


@section('content_left')

	<div class="card card-inverse col-md-{{$edit_left_md_size}} border border-info border-top-0 border-left-0 border-bottom-0">

				{{ Form::model($view_var, array('route' => array($form_update, $view_var->id), 'method' => 'put', 'files' => true, 'id' => 'EditForm')) }}
					@include($form_path, $view_var)
				{{ Form::close() }}
	</div>
@stop


<?php $api = App\Http\Controllers\BaseViewController::get_view_has_many_api_version($relations) ?>

{{-- d($relations, $edit_left_md_size) --}}

@section('content_right')
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
						{{$relation['html']}}
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
		@include ('bootstrap.panel', array ('content' => "content_$i",
											'view_header' => \App\Http\Controllers\BaseViewController::translate_view('Assigned', 'Header').' '.\App\Http\Controllers\BaseViewController::translate_view($view, 'Header' , 2),
											'md' => isset($md_size) ? $md_size : (isset($edit_right_md_size) ? $edit_right_md_size : 4)))

	@endforeach


	{{-- Alert --}}
	@if (Session::has('alert'))
		@include('bootstrap.alert', array('message' => Session::get('alert')))
		<?php Session::forget('alert'); ?>
	@endif


@stop

@section('javascript_extra')
@if(isset($panel_right))
	<script language="javascript">
	$('#loggingtab').click(function() {
		$('.tab-content').toggle();
	});
	$("#logging" ).load( "{{Route('GuiLog.filter')}}?model_id={{$view_var->id}}&model={{$view_var->table}} #IndexForm", function(){
		var table = $('table.datatable').DataTable(
		{
			language: {
				"sEmptyTable":          "{{ trans('view.jQuery_sEmptyTable') }}",
				"sInfo":                "{{ trans('view.jQuery_sInfo') }}",
				"sInfoEmpty":           "{{ trans('view.jQuery_sInfoEmpty') }}",
				"sInfoFiltered":        "{{ trans('view.jQuery_sInfoFiltered') }}",
				"sInfoPostFix":         "{{ trans('view.jQuery_sInfoPostFix') }}",
				"sInfoThousands":       "{{ trans('view.jQuery_sInfoThousands') }}",
				"sLengthMenu":          "{{ trans('view.jQuery_sLengthMenu') }}",
				"sLoadingRecords":      "{{ trans('view.jQuery_sLoadingRecords') }}",
				"sProcessing":          "{{ trans('view.jQuery_sProcessing') }}",
				"sSearch":              "{{ trans('view.jQuery_sSearch') }}",
				"sZeroRecords":         "{{ trans('view.jQuery_sZeroRecords') }}",
				"oPaginate": {
					"sFirst":           "{{ trans('view.jQuery_PaginatesFirst') }}",
					"sPrevious":        "{{ trans('view.jQuery_PaginatesPrevious') }}",
					"sNext":            "{{ trans('view.jQuery_PaginatesNext') }}",
					"sLast":            "{{ trans('view.jQuery_PaginatesLast') }}"
					},
				"oAria": {
					"sSortAscending":   "{{ trans('view.jQuery_sLast') }}",
					"sSortDescending":  "{{ trans('view.jQuery_sLast') }}"
					},
				"buttons": {
					"print":            "{{ trans('view.jQuery_Print') }}",
					"colvis":           "{{ trans('view.jQuery_colvis') }}",
					"colvisRestore":    "{{ trans('view.jQuery_colvisRestore') }}",
				}
			},
			responsive: {
				details: {
				type: 'column',
				}
			},
			dom: "Btip",
			buttons: [
				{
					extend: 'print',
					className: 'btn-sm btn-primary',
					titleAttr: "{{ trans('helper.PrintVisibleTable') }}",
					exportOptions: {columns: ':visible.content'},
				},
				{
					extend: 'collection',
					text: "{{ trans('view.jQuery_ExportTo') }}",
					titleAttr: "{{ trans('helper.ExportVisibleTable') }}",
					className: 'btn-sm btn-primary',
					autoClose: true,
					buttons: [
						{
							extend: 'csvHtml5',
							text: "<i class='fa fa-file-code-o'></i> .CSV",
							exportOptions: {columns: ':visible.content'},
							fieldSeparator: ';'
						},
						{
							extend: 'excelHtml5',
							text: "<i class='fa fa-file-excel-o'></i> .XLSX",
							exportOptions: {columns: ':visible.content'}
						},
						{
							extend: 'pdfHtml5',
							text: "<i class='fa fa-file-pdf-o'></i> .PDF",
							exportOptions: {
								columns: ':visible.content'
								},
							customize: function(doc, config) {
								var tableNode;
								for (i = 0; i < doc.content.length; ++i) {
									if(doc.content[i].table !== undefined){
									tableNode = doc.content[i];
									break;
									}
								}

								var rowIndex = 0;
								var tableColumnCount = tableNode.table.body[rowIndex].length;

								if(tableColumnCount > 6){
									doc.pageOrientation = 'landscape';
								}
							},

						},
					]
				},
				{
					extend: 'colvis',
					className: 'btn-sm btn-primary',
					titleAttr: "{{ trans('helper.ChangeVisibilityTable') }}",
					columns: ':not(.nocolvis)',
					postfixButtons: [
						{
							extend:'colvisGroup',
							className: 'dt-button btn-warning',
							text:"{{ trans('view.jQuery_colvisReset') }}",
							show:':hidden'
						},
					],
				},
			],
			fnDrawCallback: function(oSettings) {
				if ( ($('#datatable tr').length <= this.api().page.info().length) && (this.api().page.info().page == 0) ){
					$('.dataTables_paginate').hide();
					$('.dataTables_info').hide();
				}
				if ($('#datatable tr').length >= this.api().page.info().length) {
					$('.dataTables_paginate').show();
					$('.dataTables_info').show();
				}
			},
			fnAdjustColumnSizing: true,
			autoWidth: false,
			lengthMenu:  [ [10, 25, 100, 250, 500, -1], [10, 25, 100, 250, 500, "{{ trans('view.jQuery_All') }}" ] ],
			aoColumnDefs: [ {
				className: 'control',
				orderable: false,
				targets:   [0]
			},
			{
                "targets": [ 4 ],
                "visible": false,
            },
            {
                "targets": [ 5 ],
                "visible": false
            }
			],
		});
	});
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	 		$( $.fn.dataTable.tables(true) ).DataTable().responsive.recalc();
	});
	</script>
@endif
@stop
