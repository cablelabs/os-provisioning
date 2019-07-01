{{--

@param $route_name:     the base route name of this object class
@param $headline:       the link header description in HTML
@param $view_header:    the base route name of this object class
@param $create_allowd:  create button visibility
@param $delete_allowd:  delete button visibility
@param $model:          model object of current model

--}}

<?php
    $blade_type = 'index_list';
?>

@extends ('Layout.split84-nopanel')

@section('content_top')
    <li class="active">
        <a href="{{route($route_name.'.index')}}">
            {!! $model->view_icon().' '.$headline !!}
        </a>
    </li>
@stop

@section('content_left')
    {{-- Headline: means icon followed by headline --}}
    <div class="col-md-12">
        <div class="row m-b-25">
            <div class="col">
                <h3 class="card-title">
                    {!! $model->view_icon().' '.$headline !!}
                </h3>
            </div>
        {{-- Create Form --}}
        @can('create', $model)
            <div class="align-self-end m-r-20">
                @if ($create_allowed)
                    {{ Form::open(array('method' => 'GET', 'id' => 'createModel')) }}
                    <a href={{route($route_name.'.create')}} onclick="form.submit();" class="btn btn-outline-primary float-right m-b-10"
                        style="simple" data-toggle="tooltip" data-delay='{"show":"250"}' data-placement="top"
                        title="{{ \App\Http\Controllers\BaseViewController::translate_view('Create '.$model->view_headline(), 'Button' )}}">
                        <i class="fa fa-plus fa-2x" aria-hidden="true"></i>
                    </a>
                    {{ Form::close() }}
                @endif
            </div>
        @endcan
        @can('delete', $model)
            <div class="align-self-end m-r-30">
                @if ($delete_allowed)
                    <button type="submit" class="btn btn-outline-danger m-b-10 float-right" style="simple" data-toggle="tooltip" data-delay='{"show":"250"}' data-placement="top"
                    title="{{ \App\Http\Controllers\BaseViewController::translate_view('Delete', 'Button' ) }}" form="IndexForm" name="_delete">
                            <i class="fa fa-trash-o fa-2x" aria-hidden="true"></i>
                    </button>
                @endif
            </div>
        @endcan

        {{--Help Section--}}
        @include('Generic.documentation')

        </div>
    </div>

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
                        @if ((!empty($model->view_index_label()['disable_sortsearch'])) && ($model->view_index_label()['disable_sortsearch'] == [$field => 'false']))
                            <i class="fa fa-info-circle text-info" data-trigger="hover" data-container="body" data-toggle="tooltip" data-placement="top" data-delay='{"show":"250"}'
                            data-original-title="{{trans('helper.SortSearchColumn')}}"></i>
                        @elseif (!empty($model->view_index_label()['help'][$field]))
                            <i class="fa fa-info-circle text-info" data-trigger="hover" data-container="body" data-toggle="tooltip" data-placement="top" data-delay='{"show":"250"}'
                            data-original-title="{{trans('helper.'.$model->view_index_label()['help'][$field])}}"></i>
                        @endif
                        </th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody> {{-- Table DATA --}}
        </tbody>
        <tfoot> {{-- TABLE FOOTER--}}
        @if (isset($model) && method_exists( BaseController::get_model_obj() , 'view_index_label' ))
            <tr>
                <th></th>  {{-- Responsive Column --}}
                @if (isset($delete_allowed) && $delete_allowed == true)
                    <th></th> {{-- Checkbox Column if delete is allowed --}}
                @endif
                @foreach ($model->view_index_label()['index_header'] as $field)
                    @if ((!empty($model->view_index_label()['disable_sortsearch'])) && ( array_has( $model->view_index_label()['disable_sortsearch'] , $field) ) )
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
    {{-- STANDARD CONFIGURATION --}}
        {{-- Translate Datatables Base --}}
            @include('datatables.lang')
        {{-- Buttons above Datatable for export, print and change Column Visibility --}}
            @include('datatables.buttons')
        {{-- Table Footer Search fields to filter Columnwise and SAVE Filter --}}
            @include('datatables.colsearch')
        {{-- Show Pagination only when the results do not fit on one page --}}
            @include('datatables.paginate')
        responsive: {
            details: {
                type: 'column', {{-- auto resize the Table to fit the viewing device --}}
            }
        },
        autoWidth: false, {{-- Option to ajust Table to Width of container --}}
        dom: 'lBfrtip', {{-- sets order and what to show  --}}
        stateSave: true, {{-- Save Search Filters and visible Columns --}}
        stateDuration: 60 * 60 * 24, {{-- Time the State is used - set to 24h --}}
        lengthMenu:  [ [10, 25, 100, 250, 500, -1], [10, 25, 100, 250, 500, "{{ trans('view.jQuery_All') }}" ] ], {{-- Filter to List # Datasets --}}
        {{-- Responsive Column --}}
        columnDefs: [],
        aoColumnDefs: [ {
                className: 'control',
                orderable: false,
                searchable: false,
                targets:   [0]
            },
            {{-- Dont print error message, but fill NULL Fields with empty string --}}
            {
                defaultContent: "",
                targets: "_all"
            },
            @if (isset($delete_allowed) && $delete_allowed == true) {{-- show checkboxes only when needed --}}
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
    {{-- AJAX CONFIGURATION --}}
        @if (isset($model) && method_exists( $model, 'view_index_label') )
            processing: true, {{-- show loader--}}
            serverSide: true, {{-- enable Serverside Handling--}}
            deferRender: true,
            ajax: '{{ isset($ajax_route_name) && $route_name != "Config.index" ? route($ajax_route_name) : "" }}',
            {{-- generic Col Header generation --}}
                @include('datatables.genericColHeader')
        @endif
    });
});
</script>
@stop
