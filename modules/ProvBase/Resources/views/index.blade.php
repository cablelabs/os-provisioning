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
            <div class="col-md-12">
                @include('provbase::widgets.quickstart')
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="row">
            @if ($contracts_data['table'])
            @section ('weekly_contracts')
                @include('provbase::panels.weekly_contracts')
            @stop
            @include ('bootstrap.panel', array ('content' => "weekly_contracts", 'view_header' => trans('view.Dashboard_WeeklyCustomers'), 'md' => 7, 'height' => 'auto', 'i' => '1'))
            @endif
            <div class="col-md-5">
                <div class="row">
                    @DivOpen(6)
                    @include ('bootstrap.widget',
                        array (
                           'content' => 'contracts_total',
							'widget_icon' => 'users',
							'widget_bg_color' => 'green',
							'link_target' => '#anchor-contracts',
                        )
                    )
                    @DivClose()
                    @DivOpen(6)
                    @include ('bootstrap.widget',
                        array (
                            'content' => 'date',
                            'widget_icon' => 'calendar',
                            'widget_bg_color' => 'purple',
                        )
                    )
                    @DivClose()
                </div>
                @include('provbase::widgets.documentation')
            </div>
        </div>
    </div>
@stop
