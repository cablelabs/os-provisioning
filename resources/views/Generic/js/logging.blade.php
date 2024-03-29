<script language="javascript">
    $('#loggingtab').click(function() {
        $('#logging').toggle()
    })

    // $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    $('#loggingtab').click(function (e) {
        let table = $('table.datatable').DataTable(
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
            dom: "<'flow-root mb-3'B>rtip",
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
            ajax: '{!! isset($netelement) ? $netelement->getLoggingRoute() : (isset($view_var) ? Route( App\Http\Controllers\NamespaceController::get_route_name(). ".guilog", $view_var) : "") !!}',
            columns:[
                        {data: 'responsive', orderable: false, searchable: false},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'username', name: 'username'},
                        {data: 'method', name: 'method'},
                        {data: 'model', name: 'model'},
                        {data: 'model_id', name: 'model_id'},
            ],
        })
    table.buttons(0, null).container().addClass('grid gap-2 sm:flex').removeClass('btn-group')
    $( $.fn.dataTable.tables(true) ).DataTable().responsive.recalc()
    })
</script>
