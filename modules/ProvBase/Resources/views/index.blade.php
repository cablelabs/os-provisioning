@extends ('Layout.default')

@section ('contracts_total')
    <h4>{{ trans('view.dashboard.contracts') }} {{ date('m/Y') }}</h4>
    <p>
        @if ($contracts_data['total'])
            {{ $contracts_data['total'] }}
        @else
            {{ trans('view.dashboard.noContracts') }}
        @endif
    </p>
@stop

@section ('date')
    <h4>{{ trans('view.dashboard.date') }}</h4>
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
                            <i style="font-size: 25px;" class="img-center fa fa-file-code-o p-10"></i><br>
                            <span class="username text-ellipsis text-center">Firmwares</span>
                        </span>'))
                    !!}

                    {!! HTML::decode (HTML::linkRoute('CustomerTopo.show_impaired',
                        '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
                            <i style="font-size: 25px;" class="img-center fa fa-hdd-o text-danger p-10"></i><br>
                            <span class="username text-ellipsis text-center">'.trans('view.dashboard.impairedModem').'</span>
                        </span>'))
                    !!}

                    <a href="/genieacs/">
                        <span class="btn btn-dark p-10 m-5 m-r-10 text-center">
                            <img src="{{asset('images/genieacs.svg')}}" height="45"><br>
                            <span class="username text-ellipsis text-center">GenieACS</span>
                        </span>
                    </a>

                    {{-- reference link --}}
                    <div class="stats-link noHover"><a href="#"><br></a></div>

                </div>
            @DivClose()
            </div>
                <div class="col-md-4">
                    @include('Generic.widgets.moduleDocu', [ 'urls' => [
                        'documentation' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/Provisioning',
                        'youtube' => 'https://youtu.be/RjMlhKQXgU4',
                        'forum' => 'https://devel.roetzer-engineering.com/confluence/display/nmsprimeforum/Provisioning+General',
                    ]])
                </div>
            </div>
        </div>
        </div>

@stop
