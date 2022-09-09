@extends ('Layout.split84-nopanel')

@section('content_top')
    <li class="active"><a href={{route($route_name.'.index')}}>
    {{ trans_choice('view.Header_'.$route_name, 2) }}</a>
    </li>
@stop

@section('content_left')
    <h1 class="page-header"><i class="fa fa-hdd-o"></i>{{ $headline }}</h1>

    <table class="table table-hover datatable table-bordered d-table" id="datatable">
        <thead>
            <tr>
                <th style="text-align:center; vertical-align:middle;">CWMP ID</th>
                <th style="text-align:center; vertical-align:middle;">{{ trans('dt_header.modem.last_inform') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($discoveredDevices as $device)
                <tr class="{{ $device[2] }}">
                    <td>{{ $device[0] }}</td>
                    <td>{{ $device[1] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop

@section('javascript')
<script>
    let table = $('table.datatable').DataTable({
        fixedHeader: true,
        lengthMenu:  [ [10, 25, 100, 250, 500, -1], [10, 25, 100, 250, 500, "{{ trans('view.jQuery_All') }}" ] ], {{-- Filter to List # Datasets --}}
        @include('datatables.lang')
        @include('datatables.paginate')
    }).draw()
</script>
@stop
