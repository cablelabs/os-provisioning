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
	{{-- Headline: means icon followed by headline --}}
    @DivOpen(12)
        <div class="row m-b-25">
            <div class="col">
                <h3 class="card-title">
                @if (isset($view_var) && is_array($view_var) )
                {{ \App\Http\Controllers\BaseViewController::__get_view_icon($view_var[0]).' '}}
                @else
                {{ $model->view_icon().' '}}
                @endif
                {{$headline}}
            </div>
        {{-- Create Form --}}
            <div class="align-self-end m-r-20">
                @if ($create_allowed)
                    {{ Form::open(array('route' => $route_name.'.create', 'method' => 'GET')) }}
                    <button class="btn btn-outline-primary float-right m-b-10" style="simple" data-toggle="tooltip" data-delay='{"show":"250"}' data-placement="top"
                    title="{{ \App\Http\Controllers\BaseViewController::translate_view('Create '.$b_text, 'Button' )}}">
                        <i class="fa fa-plus fa-2x" aria-hidden="true"></i>
                    </button>
                    {{ Form::close() }}
                @endif
            </div>
            <div class="align-self-end">
                @if ($delete_allowed)
                    <button class="btn btn-outline-danger m-b-10 float-right" style="simple" data-toggle="tooltip" data-delay='{"show":"250"}' data-placement="top"
                    title="{{ \App\Http\Controllers\BaseViewController::translate_view('Delete', 'Button' ) }}" form="IndexForm">
                            <i class="fa fa-trash-o fa-2x" aria-hidden="true"></i>
                    </button>
                @endif
            </div>
        </div>
    @DivClose()

	@include('Generic.above_infos')

    {{-- database entries inside a form with checkboxes to be able to delete one or more entries --}}
    {{ Form::open(array('route' => array($route_name.'.destroy', 0), 'method' => 'delete', 'id' => 'IndexForm')) }}
    {{-- INIT DT --}}
    <table class="table table-hover datatable table-bordered d-table" id="datatable">
        {{-- Get Headerdata and translate with translation files --}}
        <thead> {{-- TABLE HEADER --}}
            <tr>
                <th class="nocolvis" style="min-width:20px;width:20px;"></th> {{-- Responsive Column --}}
                @if (isset($delete_allowed) && $delete_allowed == true) {{-- Checkbox Column if delete is allowed --}}
                    <th class="nocolvis" id="selectall" style="text-align:center; vertical-align:middle;min-width:20px;;width:20px;">
                        <input id ="allCheck" data-trigger="hover" style='simple' type='checkbox' value='1' data-container="body" data-toggle="tooltip" data-placement="top"
                        data-delay='{"show":"250"}' data-original-title="{{\App\Http\Controllers\BaseViewController::translate_label('Select All')}}">
                    </th>
                @endif
                {{-- Get Header if possible with new Format - for Backwards compatibility old one stays --}}
                @if (isset($model) && method_exists( BaseController::get_model_obj() , 'view_index_label' ) && is_array($model->view_index_label()) && isset($model->view_index_label()['index_header']))
                    @foreach ($model->view_index_label()['index_header'] as $field)
                        <th class="content" style="text-align:center; vertical-align:middle;">{{ trans('dt_header.'.$field).' ' }}
                        @if ((!empty($model->view_index_label()['sortsearch'])) && ($model->view_index_label()['sortsearch'] == [$field => 'false']))
                            <i class="fa fa-info-circle text-info" data-trigger="hover" data-container="body" data-toggle="tooltip" data-placement="top" data-delay='{"show":"250"}'
                            data-original-title="{{trans('helper.SortSearchColumn')}}"></i>
                        @endif
                        </th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody> {{-- Table DATA --}}
        </tbody>
        <tfoot> {{-- TABLE FOOTER--}}
        @if (isset($model) && isset($view_var) && method_exists( BaseController::get_model_obj() , 'view_index_label' ))
            <tr>
                <th></th>  {{-- Responsive Column --}}
                @if (isset($delete_allowed) && $delete_allowed == true)
                    <th></th> {{-- Checkbox Column if delete is allowed --}}
                @endif
                @foreach ($model->view_index_label()['index_header'] as $field)
                    @if ((!empty($model->view_index_label()['sortsearch'])) && ( array_has( $model->view_index_label()['sortsearch'] , $field) ) )
                        <th></th>
                    @else
                        <th class="searchable"></th>
                    @endif
                @endforeach
            </tr>
        @endif
        </tfoot>
    </table>
    {{ Form::close() }}
@stop

@section('javascript')
{{-- JS DATATABLE CONFIG --}}
<script language="javascript">
    $(document).ready(function() {
        var table = $('table.datatable').DataTable(
            {
    {{-- STANDARD DT CONFIGURATION --}}
                {{-- Translate Datatables --}}
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
                {{-- auto resize the Table to fit the viewing device --}}
                responsive: {
                    details: {
                        type: 'column',
                    }
                },
                {{-- Option to ajust Table to Width of container --}}
                autoWidth: false,
                {{-- sets order and what to show  --}}
                dom: 'lBfrtip',
                {{-- Save Search Filters and visible Columns --}}
                stateSave: true,
                {{-- Buttons above Datatable for export, print and change Column Visibility --}}
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
                {{-- Add Table Footer Search fields to filter Columnwise and SAVE Filter --}}
                initComplete: function () {
                    this.api().columns().every(function () {
                        var column = this;
                        var input = document.createElement('input');
                        input.classList.add('form-control');
                        input.classList.add('input-sm');
                        input.classList.add('select2');
                        if ($(this.footer()).hasClass('searchable')){
                            $(input).appendTo($(column.footer()).empty())
                            .on('keyup', function () {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                column.search(val ? val : '', true, false).draw();
                            });
                        }
                        $('.select2').css('width', "100%");
                    });
                    var state = this.api().state.loaded();
                    if (state) {
                        this.api().columns().eq(0).each(function (colIdx) {
                            var colSearch = state.columns[colIdx].search;
                            if (colSearch.search) {
                                $('input', this.column(colIdx).footer()).val(colSearch.search);
                            }
                        });
                    }
                    $(this).DataTable().columns.adjust().responsive.recalc();
                },
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
                {{-- Filter to List # Datasets --}}
                lengthMenu:  [ [10, 25, 100, 250, 500, -1], [10, 25, 100, 250, 500, "{{ trans('view.jQuery_All') }}" ] ],
                {{-- Add Columns - First 2 Columns are for Responsive Button and Input Checkbox --}}
    {{-- DT AJAX CONFIGURATION --}}
                @if (isset($model) && isset($view_var) && method_exists( $view_var, 'view_index_label') )
                {{-- enable Serverside Handling--}}
                processing: true,
                serverSide: true,
                deferRender: true,
                ajax: '{{ isset($route_name) && $route_name!= "Config.index"  ? route($route_name.'.data') : "" }}',
                columns:[
                            {data: 'responsive', orderable: false, searchable: false},
                    @if (isset($delete_allowed) && $delete_allowed == true)
                            {data: 'checkbox', orderable: false, searchable: false},
                    @endif
                    @if (isset($view_var->view_index_label()['index_header']))
                        @foreach ($view_var->view_index_label()['index_header'] as $field)
                            @if ( starts_with($field, $view_var->view_index_label()["table"].'.'))
                                {data: '{{ substr($field, strlen($view_var->view_index_label()["table"]) + 1) }}', name: '{{ $field }}'},
                            @else
                                {data: '{{ $field }}', name: '{{$field}}',
                                searchable: {{ isset($view_var->view_index_label()["sortsearch"][$field]) ? "false" : "true" }},
                                orderable:  {{ isset($view_var->view_index_label()["sortsearch"][$field]) ? "false" : "true" }}
                                },
                            @endif
                        @endforeach
                    @endif
                ],
                {{-- Set Sorting if order_by is set -> Standard is ASC of first Column --}}
                @if (isset($view_var->view_index_label()['order_by']))
                    order:
                    @foreach ($view_var->view_index_label()['order_by'] as $columnindex => $direction)
                        @if (isset($delete_allowed) && $delete_allowed == true)
                            [{{$columnindex + 2}}, '{{$direction}}'],
                        @else
                            [{{$columnindex + 1}}, '{{$direction}}'],
                        @endif
                    @endforeach
                @endif
                {{-- Responsive Column --}}
                aoColumnDefs: [ {
                    className: 'control',
                    orderable: false,
                    searchable: false,
                    targets:   [0]
                },
                @if (isset($delete_allowed) && $delete_allowed == true)
                {
                    className: 'index_check',
                    orderable: false,
                    searchable: false,
                    targets:   [1]
                },
                @endif
                {
                    targets :  "_all",
                    className : 'ClickableTd',
                } ],
        @elseif (method_exists( BaseController::get_model_obj() , 'view_index_label' ))
            aoColumnDefs: [ {
                className: 'control',
                orderable: false,
                targets:   [0]
            },
            @if (isset($delete_allowed) && $delete_allowed == true)
            {
                className: 'index_check',
                orderable: false,
                targets:   [1]
            },
            @endif
            ],
        @endif
        });
    });
</script>
@stop
