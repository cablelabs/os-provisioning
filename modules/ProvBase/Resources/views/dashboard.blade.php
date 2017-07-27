@extends ('Layout.default')

@section ('quickstart')
    <!-- TODO: move to a seperate file -->
    <!-- TODO: use translate lang -->
    <ul class="registered-users-list clearfix">
        <li>
            {{ HTML::decode (HTML::linkRoute('Contract.index',
                '<h1><div class="text-center"><i class="img-center fa fa-address-book-o"></i></div></h1>
                 <h4 class="username text-ellipsis text-center">Show Contracts<small>Easy</small></h4>')) }}
        </li>
        <li>
            {{ HTML::decode (HTML::linkRoute('Contract.create',
                '<h1><div class="text-center"><i class="img-center fa fa-address-book-o"></i></div></h1>
                 <h4 class="username text-ellipsis text-center">Add Contract<small>Easy</small></h4>')) }}
        </li>
        <li>
                <h1><div class="text-center"><i class="img-center fa fa-ticket"></i></div></h1>
                 <h4 class="username text-ellipsis text-center">Add Ticket<small>Easy</small></h4>
        </li>
    </ul>
@stop

@section('content')
    <div class="col-md-12">

        <h1 class="page-header">{{ $title }}</h1>

        <div class="row">
            {{-- Contracts --}}
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-green">
                    {{-- icon --}}
                    <div class="stats-icon">
                        <i class="fa fa-users"></i>
                    </div>

                    {{-- info/data --}}
                    <div class="stats-info">
                        <h4>{{ \App\Http\Controllers\BaseViewController::translate_view('Contracts', 'Dashboard') }} {{ date('m/Y') }}</h4>
                        <p>
                            @if ($contracts == 0)
                                {{ \App\Http\Controllers\BaseViewController::translate_view('NoContracts', 'Dashboard') }}
                            @else
                                {{ $contracts }}
                            @endif
                        </p>
                    </div>

                    {{-- refernce link --}}
                    <div class="stats-link">
                        <a href="javascript:;">
                            {{ \App\Http\Controllers\BaseViewController::translate_view('LinkDetails', 'Dashboard') }} <i class="fa fa-arrow-circle-o-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Income --}}
            @if (\PPModule::is_active('billingbase'))
                @if ($allowed_to_see['accounting'] === true)
                    <div class="col-md-3 col-sm-6">
                        <div class="widget widget-stats bg-blue">
                            {{-- icon --}}
                            <div class="stats-icon">
                                <i class="fa fa-euro"></i>
                            </div>

                            {{-- info/data --}}
                            <div class="stats-info">
                                <h4>{{ \App\Http\Controllers\BaseViewController::translate_view('Net Income', 'Dashboard') }} {{ date('m/Y') }}</h4>
                                <p>
                                    @if (isset($income['total']))
                                        {{ number_format($income['total'], 2, ',', '.') }}
                                    @else
                                        {{ number_format(0, 2, ',', '.') }}
                                    @endif
                                </p>
                            </div>

                            {{-- refernce link --}}
                            <div class="stats-link">
                                <a href="javascript:;">
                                    {{ \App\Http\Controllers\BaseViewController::translate_view('LinkDetails', 'Dashboard') }} <i class="fa fa-arrow-circle-o-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Placeholder --}}
            @if (\PPModule::is_active('provvoipenvia'))
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-aqua">
                    {{-- icon --}}
                    <div class="stats-icon">
                        <i class="fa fa-info"></i>
                    </div>

                    {{-- info/data --}}
                    <div class="stats-info">
                        <h4>PLACEHOLDER</h4>
                        <p>ToDo's ProvVoipEnvia</p>
                    </div>

                    {{-- refernce link --}}
                    <div class="stats-link">
                        <a href="javascript:;">
                            {{ \App\Http\Controllers\BaseViewController::translate_view('LinkDetails', 'Dashboard') }} <i class="fa fa-arrow-circle-o-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Date --}}
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-purple">
                    {{-- icon --}}
                    <div class="stats-icon">
                        <i class="fa fa-calendar"></i>
                    </div>

                    {{-- info/data --}}
                    <div class="stats-info">
                        <h4>{{ \App\Http\Controllers\BaseViewController::translate_view('Date', 'Dashboard') }}</h4>
                        <p>{{ date('d.m.Y') }}</p>
                    </div>

                    {{-- refernce link --}}
                    <div class="stats-link">
                        <a href="javascript:;">&nbsp;</a>
                    </div>
                </div>
            </div>
        </div>

        <br><br>

        <div class="row">
            @if ($contracts > 0)
                <div class="col-md-8">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">
                            <h4 class="panel-title">{{ \App\Http\Controllers\BaseViewController::translate_view('ContractAnalytics', 'Dashboard') }}</h4>
                        </div>
                        <div class="panel-body">
                            <div class="height-sm" style="padding: 0px; position: relative;">
                                <canvas id="contracts-chart" height="75px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                @DivOpen(4)
                    @include ('bootstrap.panel', array ('content' => "quickstart", 'view_header' => 'Quickstart', 'md' => 12, 'height' => 'auto'))
                @DivClose()
            @endif

            @if (\PPModule::is_active('billingbase'))
                @if ($allowed_to_see['accounting'] === true)
                    @if (isset($income['total']))
                        <div class="col-md-4">
                            <div class="panel panel-inverse">
                                <div class="panel-heading">
                                    <h4 class="panel-title">{{ \App\Http\Controllers\BaseViewController::translate_view('IncomeAnalytics', 'Dashboard') }}</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="height-sm" style="padding: 0px; position: relative;">
                                        <canvas id="income-chart" height="160px"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>
@stop

<script type="text/javascript">
    if (typeof window.jQuery == 'undefined') {
        document.write('<script src="{{asset('components/assets-admin/plugins/jquery/jquery-1.9.1.min.js')}}">\x3C/script>');
    }
</script>
<script src="{{asset('components/assets-admin/plugins/chart/Chart.min.js')}}"></script>

<script type="text/javascript">

    window.onload = function() {

        // line chart contracts
        var chart_data_contracts = {{ json_encode($chart_data_contracts) }};

        if (chart_data_contracts.length != 0) {

            var labels = chart_data_contracts['labels'];
            var contracts = chart_data_contracts['contracts'];
            var ctx = document.getElementById('contracts-chart').getContext('2d');
            var contractChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: contracts,
                        backgroundColor: "rgba(0, 172, 172, 0.8)",
                    }],
                },
                options: {
                    legend: {
                        display: false
                    }
                }
            });
        }

        // bar chart income
        var chart_data_income = {{ json_encode($chart_data_income) }};

        if (chart_data_income.length != 0) {

            var labels = chart_data_income['labels'];
            var incomes = chart_data_income['data'];
            var ctx = document.getElementById('income-chart').getContext('2d');
            var incomeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: incomes,
                        backgroundColor: [
                            "rgba(255, 206, 86, 0.8)",
                            "rgba(75, 192, 192, 0.8)",
                            "rgba(54, 162, 235, 0.8)",
                            "rgba(153, 102, 255, 0.8)",
                        ]
                    }],
                },
                options: {
                    legend: {
                        display: false
                    }
                }
            });
        }
    }
</script>
