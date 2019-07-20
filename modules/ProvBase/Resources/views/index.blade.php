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

@section('javascript')
    <script src="{{asset('components/assets-admin/plugins/chart/Chart.min.js')}}"></script>
    <script language="javascript">

        $(window).on('localstorage-position-loaded load', function () {
            // line chart contracts
            var chart_data_contracts = {!! $contracts_data ? json_encode($contracts_data['chart']) : '{}' !!};

            if (Object.getOwnPropertyNames(chart_data_contracts).length != 0) {

                var labels = chart_data_contracts['labels'],
                    contracts = chart_data_contracts['contracts'],
                    internet = chart_data_contracts['Internet_only'],
                    voip = chart_data_contracts['Voip_only'],
                    internetAndVoip = chart_data_contracts['Internet_and_Voip'],
                    ctx = document.getElementById('contracts-chart').getContext('2d');

                var contractChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                                @if (Module::collections()->has('BillingBase'))
                            {
                                label: 'VoIP',
                                data: voip,
                                pointBackgroundColor: 'rgb(42, 98, 254, 1)',
                                borderColor: 'rgb(42, 98, 254, 1)',
                                backgroundColor: 'rgb(42, 98, 254, 0.3)',
                                cubicInterpolationMode: 'monotone'
                            }, {
                                label: 'Internet & Voip',
                                data: internetAndVoip,
                                pointBackgroundColor: 'rgb(12, 40, 110, 1)',
                                borderColor: 'rgb(12, 40, 110, 1)',
                                backgroundColor: 'rgb(12, 40, 110, 0.3)',
                                cubicInterpolationMode: 'monotone'
                            }, {
                                label: 'Internet',
                                data: internet,
                                pointBackgroundColor: 'rgb(0, 170, 132, 1)',
                                borderColor: 'rgb(0, 170, 132, 1)',
                                backgroundColor: 'rgb(0, 170, 132, 0.3)',
                                cubicInterpolationMode: 'monotone'
                            },
                                @endif
                            {
                                label: "{!! trans('messages.active contracts') !!}",
                                data: contracts,
                                pointBackgroundColor: 'rgb(2, 207, 211, 1)',
                                borderColor: 'rgb(2, 207, 211, 1)',
                                backgroundColor: 'rgb(2, 207, 211, 0.3)',
                                cubicInterpolationMode: 'monotone'
                            }],
                    },
                    options: {
                        animation: {
                            duration: 0,
                        },
                        legend: {
                            display: true,
                        },
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: false,
                                }
                            }]
                        }
                    }
                });
            }
        });
    </script>
@stop
