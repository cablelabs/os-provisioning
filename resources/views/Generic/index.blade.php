<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
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
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h3 class="text-2xl flex-1 dark:text-primary-gray">
                {!! $model->view_icon().' '.$headline !!}
            </h3>
        {{-- Create Form --}}
        @can('create', $model)
            <div class="align-self-end m-r-20">
                @if ($create_allowed)
                    {{ Form::open(array('method' => 'GET', 'id' => 'createModel')) }}
                    <a href={{route($route_name.'.create')}} onclick="form.submit();" class="btn btn-outline-primary float-right"
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
                    <button type="submit" class="btn btn-outline-danger float-right" style="simple" data-toggle="tooltip" data-delay='{"show":"250"}' data-placement="top"
                        title="{{ \App\Http\Controllers\BaseViewController::translate_view('Delete', 'Button' ) }}" form="IndexForm" name="_delete">
                        <i class="fa fa-trash-o fa-2x" aria-hidden="true"></i>
                    </button>
                @endif
            </div>
        @endcan

        {{--Help Section--}}
        @include('Generic.documentation')
        </div>

        @if (Request::has('show_filter'))
        <div class="mt-4">
                <div id="filter">
                    <i class="fa fa-filter" style="simple" data-toggle="tooltip" data-delay='{"show":"250"}' data-placement="right" title="{{ trans("messages.hardFilter") }}"></i>
                    <a class="badge badge-primary" href="{{ Request::url() }}"
                        style="simple" data-toggle="tooltip" data-delay='{"show":"250"}' data-placement="right"
                        title="{{ trans('messages.removeFilter') }}">
                        {{ trans("view.filter.{$filter['key']}", ['data' => $filter['data']]) }}
                        <i class="fa fa-close p-l-5"></i>
                    </a>
                </div>
            {{-- TODO: Make this generic and let the user select the filter from dropdown or via link --}}
        </div>
        @elseif (! empty($model::AVAILABLE_FILTERS) && $model instanceof Modules\ProvVoipEnvia\Entities\EnviaOrder)
        <div class="mt-4">
            <a href="?show_filter=action_needed" target="_self"> <i class="fa fa-filter"></i> {{ trans('provvoipenvia::view.enviaOrder.showInteractionNeeding')}} </a>
        </div>
        @endif
    </div>

    @include('Generic.above_infos')

    {{-- database entries inside a form with checkboxes to be able to delete one or more entries --}}
    @if ($delete_allowed)
        {{ Form::open(array('route' => array($route_name.'.destroy', 0), 'method' => 'delete', 'id' => 'IndexForm')) }}
    @endif

    {{-- INIT DT --}}
    <table class="table table-hover datatable table-bordered d-table" id="datatable">
        {{-- Get Headerdata and translate with translation files --}}
        <thead> {{-- TABLE HEADER --}}
            <tr>
                @if (isset($delete_allowed) && $delete_allowed == true) {{-- Checkbox Column if delete is allowed --}}
                    <th class="nocolvis" id="selectall" style="text-align:center; vertical-align:middle;min-width:20px;;width:20px;">
                        <input id ="allCheck" data-trigger="hover" style='simple' type='checkbox' value='1' data-container="body" data-toggle="tooltip" data-placement="top"
                        data-delay='{"show":"250"}' data-original-title="{{\App\Http\Controllers\BaseViewController::translate_label('Select All')}}">
                    </th>
                @endif
                {{-- Get Header if possible with new Format - for Backwards compatibility old one stays --}}
                @if (isset($model) && $methodExists && is_array($indexTableInfo) && isset($indexTableInfo['index_header']))
                    @foreach ($indexTableInfo['index_header'] as $field)
                        <th class="content" style="text-align:center; vertical-align:middle;">{{ trans('dt_header.'.$field).' ' }}
                        @if ((! empty($indexTableInfo['disable_sortsearch'])) && ($indexTableInfo['disable_sortsearch'] == [$field => 'false']))
                            <i class="fa fa-info-circle text-info" data-trigger="hover" data-container="body" data-toggle="tooltip" data-placement="top"
                                data-delay='{"show":"250"}' data-original-title="{{trans('helper.SortSearchColumn')}}"></i>
                        @elseif (! empty($indexTableInfo['help'][$field]))
                            <i class="fa fa-info-circle text-info" data-trigger="hover" data-container="body" data-toggle="tooltip" data-placement="top"
                                data-delay='{"show":"250"}' data-original-title="{{trans('helper.'.$indexTableInfo['help'][$field])}}"></i>
                        @endif
                        </th>
                    @endforeach
                @endif
            </tr>
        </thead>

        <tbody> {{-- Table DATA --}}
        </tbody>

        <tfoot> {{-- TABLE FOOTER--}}
        @if (isset($model) && $methodExists)
            <tr>
                @if (isset($delete_allowed) && $delete_allowed == true)
                    <th></th> {{-- Checkbox Column if delete is allowed --}}
                @endif
                @foreach ($indexTableInfo['index_header'] as $field)
                    @if ((!empty($indexTableInfo['disable_sortsearch'])) && ( Illuminate\Support\Arr::has( $indexTableInfo['disable_sortsearch'] , $field) ) )
                        <th></th>
                    @else
                        <th class="searchable"></th>
                    @endif
                @endforeach
            </tr>
        @endif
        </tfoot>

    </table>

    @if ($delete_allowed)
        {{ Form::close() }}
    @endif
@stop

@section('javascript')
{{-- JS DATATABLE CONFIG --}}
<script language="javascript">
$(document).ready(function() {
    // Show Buttons and enable lazy loading
    window.JSZip = true
    window.pdfMake = true

    let order = [
        @if (isset($indexTableInfo['order_by']))
            @foreach ($indexTableInfo['order_by'] as $columnindex => $direction)
                @if (isset($delete_allowed) && $delete_allowed == true)
                    [{{$columnindex + 2}}, '{{$direction}}'],
                @else
                    [{{$columnindex + 1}}, '{{$direction}}'],
                @endif
            @endforeach
        @endif
    ];

    let table = $('table.datatable').DataTable({
        {{-- STANDARD CONFIGURATION --}}
        {{-- Translate Datatables Base --}}
        @include('datatables.lang')
        {{-- Buttons above Datatable for export, print and change Column Visibility --}}
        @include('datatables.buttons')
        {{-- Table Footer Search fields to filter Columnwise and SAVE Filter --}}
        @include('datatables.colsearch')
        {{-- Show Pagination only when the results do not fit on one page --}}
        @include('datatables.paginate')
        scrollX: true,
        autoWidth: false, {{-- Option to ajust Table to Width of container --}}
        dom: 'lBfrtip', {{-- sets order and what to show  --}}
        stateSave: true, {{-- Save Search Filters and visible Columns --}}
        stateDuration: 0, // 60 * 60 * 24, {{-- Time the State is used - set to 24h --}}
        lengthMenu:  [ [10, 25, 100, 250, 500, -1], [10, 25, 100, 250, 500, "{{ trans('view.jQuery_All') }}" ] ], {{-- Filter to List # Datasets --}}
        order: order,
        {{-- Dont print error message, but fill NULL Fields with empty string --}}
        columnDefs: [{
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
            className: 'nocolvis',
            targets: {{ (isset($delete_allowed) && $delete_allowed == true) ? '[2]' : '[1]'}},
        },
        {
            targets :  "_all",
            className : 'ClickableTd',
        } ],
        {{-- AJAX CONFIGURATION --}}
        @if (isset($model) && method_exists( $model, 'view_index_label') )
            processing: true, {{-- show loader--}}
            serverSide: true, {{-- enable Serverside Handling--}}
            deferRender: true,
            deferLoading: true,
            ajax: '{{ isset($ajax_route_name) && $route_name != "Config.index" ? route($ajax_route_name) : "" }}',
            {{-- generic Col Header generation --}}
            @include('datatables.genericColHeader')
        @endif
    });

    @if (isset($indexTableInfo['globalFilter']) && Request::has('show_filter'))
        @foreach ($indexTableInfo['globalFilter'] as $col => $search)
            setGlobalFilter('{{ $col }}', '{{ $search }}')
        @endforeach
    @elseif (isset($indexTableInfo['globalFilter']) && !Request::has('show_filter'))
        @foreach ($indexTableInfo['globalFilter'] as $col => $search)
            removeGlobalFilter('{{ $col }}')
        @endforeach
    @endif

    table.draw()

    function setGlobalFilter(col, search)
    {
        let idx = getKeyByValue(table.columns().dataSrc(), col)
        let searchField = $('input', table.column(idx).footer())

        table.column(idx).search(search)
        searchField.val(search)
        searchField.keyup(function () {
            let filter = $('#filter')

            if (filter.length) {
                filter.remove()
            }
        })
    }

    function removeGlobalFilter(col)
    {
        let idx = getKeyByValue(table.columns().dataSrc(), col)

        $('input', table.column(idx).footer()).val('')
        table.column(idx).search('')
    }

    function getKeyByValue(object, value)
    {
        return Object.keys(object).find(key => object[key] === value);
    }
});
</script>
@stop
