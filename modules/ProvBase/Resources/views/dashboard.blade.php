@extends ('Layout.default')

<link href="{{asset('components/assets-admin/plugins/switchery/switchery.css')}}" rel="stylesheet" />

@section('content')
    <div class="col-md-12">

        <h1 class="page-header">{{ $title }}</h1>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <h4 class="panel-title">Aktive Verträge <?php echo date('m/Y'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="widget widget-stats bg-aqua-lighter">
                            <div class="stats-icon">
                                <i class="fa fa-globe fa-fw"></i>
                            </div>
                            <div class="stats-info">
                                @if (count($contracts) == 0)
                                    <p><h4>Keine Verträge vorhanden.</h4></p>
                                @else
                                    <h4>total:</h4>
                                    <p><h1 style="color: #ffffff">{{ $contracts['count_all'] }}</h1></p>
                                    <div class="stats-desc">
                                        <?php
                                            $diff = $contracts['count_all'] - $contracts['count_filtered'];
                                        ?>
                                        @if ($contracts['period'] == 'lastMonth')
                                            Veränderung zum Vormonat: {{ $diff }}
                                        @elseif ($contracts['period'] == 'dayPeriod')
                                            Veränderung in den letzten {{ $contracts['days'] }} Tagen: {{ $diff }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        {{ Form::open(array('route' => array('Dashboard.index', 0), 'method' => 'POST', 'files' => false)) }}
                            <div class="input-group">
                                <input type="text" class="form-control input-sm" name="datefilter" placeholder="Zeitraum in Tagen">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary btn-sm" type="submit">Filter</button>
                                </span>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>

            @if (count($contracts) > 0)
                <div class="col-md-6 col-sm-6">
                    <div class="panel panel-inverse">
                        <div class="panel-heading"><h4 class="panel-title">Analyse letzte 12 Monate</h4></div>
                        <div class="panel-body">
                            <!-- div id="contracts-legend" style="float: right; padding: 25px;" -->
                                <!-- Legende -->
                            <!-- /div -->
                            <div id="contracts-chart" style="width: 100%; height: 300px;">
                                <!-- Chart -->
                                <canvas id="chart" height="100%"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

<script type="text/javascript">
    if (typeof window.jQuery == 'undefined') {
        document.write('<script src="{{asset('components/assets-admin/plugins/jquery/jquery-1.9.1.min.js')}}">\x3C/script>');
    }
</script>

<script src="{{asset('components/assets-admin/plugins/flot/jquery.flot.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/flot/jquery.flot.categories.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/chart/Chart.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/switchery/switchery.js')}}"></script>
<script src="{{asset('components/assets-admin/js/form-slider-switcher.demo.js')}}"></script>

<script type="text/javascript">

    window.onload = function() {

        // line chart contracts
        var chart_data = {{ json_encode($chart_data_contracts) }};

        if (chart_data.length != 0) {

            var labels = chart_data['labels'];
            var contracts = chart_data['contracts'];
            var ctx = document.getElementById('chart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: contracts,
                        backgroundColor: "rgba(0,128,0,0.6)",
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
