@extends ('Layout.default')

@section ('contracts_total')
    <h4>{{ \App\Http\Controllers\BaseViewController::translate_view('Contracts', 'Dashboard') }} {{ date('m/Y') }}</h4>
    <p>
        @if ($contracts_data['total'])
            {{ $contracts_data['total'] }}
        @else
            {{ \App\Http\Controllers\BaseViewController::translate_view('NoContracts', 'Dashboard') }}
        @endif
    </p>
@stop

@section ('date')
    <h4>{{ \App\Http\Controllers\BaseViewController::translate_view('Date', 'Dashboard') }}</h4>
    <p>{{ date('d.m.Y') }}</p>
@stop

@section('content')

    <div class="col-md-12">

        <h1 class="page-header">{{ $title }}</h1>

        {{--Quickstart--}}

        <div class="row">
            @DivOpen(7)
            @include('provbase::widgets.quickstart')
            @DivClose()
            @DivOpen(2)
            @DivClose()

            @DivOpen(3)
            @include ('bootstrap.widget',
                array (
                    'content' => 'date',
                    'widget_icon' => 'calendar',
                    'widget_bg_color' => 'purple',
                )
            )
            @DivClose()
        </div>
    </div>
    <div class="col-md-12">
        <div class="row">
            @DivOpen(3)
            @include ('bootstrap.widget',
                array (
                   'content' => 'contracts_total',
                    'widget_icon' => 'users',
                    'widget_bg_color' => 'green',
                    'link_target' => '#anchor-contracts',
                )
            )
            @DivClose()
        </div>
        <div class="row">
            @DivOpen(4)
            <div class="widget widget-stats bg-blue">
                {{-- info/data --}}
                <div class="stats-info text-center">

                    {!! HTML::decode (HTML::linkRoute('Modem.firmware',
                        '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
                            <i style="font-size: 25px;" class="img-center fa fa-file-code-o p-10"></i><br />
                            <span class="username text-ellipsis text-center">Firmwares</span>
                        </span>'))
                    !!}

                    {!! HTML::decode (HTML::linkRoute('CustomerTopo.show_impaired',
                        '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
                            <i style="font-size: 25px;" class="img-center fa fa-hdd-o text-danger p-10"></i><br />
                            <span class="username text-ellipsis text-center">'.trans('view.Dashboard_ImpairedModem').'</span>
                        </span>'))
                    !!}

                    {{-- reference link --}}
                    <div class="stats-link"><a href="#"><br></a></div>

                </div>
            @DivClose()
            </div>
                <div class="col-md-4">
                    @include('provbase::widgets.documentation')
                </div>
            </div>
        </div>
        </div>

@stop
