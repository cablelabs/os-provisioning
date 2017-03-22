@extends ('Layout.default')

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
                                <h4>total:</h4>
                                <p><h1 style="color: #ffffff">{{ count($contracts['till_now']) }}</h1></p>
                                <div class="stats-desc">
                                    @if (!isset($contracts['days']))
                                        Veränderung zum Vormonat: {{ count($contracts['till_now']) - count($contracts['period']) }}
                                    @else
                                        Veränderung in den letzten {{ $contracts['days'] }} Tagen: {{ count($contracts['till_now']) - count($contracts['period']) }}
                                    @endif
                                </div>
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

            <div class="col-md-6 col-sm-6">
                <div class="panel panel-inverse">
                    <div class="panel-heading"><h4 class="panel-title">Analyse letzte 12 Monate</h4></div>
                    <div class="panel-body">
                        <div id="contracts-legend" style="float: right; padding: 25px;">
                            <!-- Legende -->
                        </div>
                        <div id="contracts-chart" style="width: 100%; height: 300px;">
                            <!-- Chart -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br><br><br>

        @if ($show_sales)
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">
                            <h4 class="panel-title">Umsatz <?php echo date('Y'); ?></h4>
                        </div>
                        <div class="panel-body">
                            <div class="widget widget-stats bg-green-lighter">
                                <div class="stats-icon">
                                    <i class="fa fa-euro fa-fw"></i>
                                </div>
                                <div class="stats-info">
                                    <h4>total:</h4>
                                    <?php
                                        $sales_total = 0;
                                        if (isset($sales[date('Y')])) {
											$sales_total = $sales[date('Y')]['total'];
                                        } elseif (isset($sales['current_month'])) {
											$sales_total = number_format($sales['current_month']['total'], 2, ',', '');
                                        }
                                    ?>
                                    <p>{{ $sales_total }}</p>
                                    <div class="stats-desc">
                                        <?php
                                            $sales_diff = 0;

										    if (isset($sales[date('Y')])) {
                                                $sales_diff = $sales[date('Y')]['total'] - $sales[date('Y') - 1]['total'];
                                                $sales_diff = number_format($sales_diff, 2, ',', '');
                                                $periode = 'Vorjahr';
                                            } elseif(isset($sales['current_month'])) {
												$sales_diff = $sales['current_month']['total'] - $sales['last_month']['total'];
												$sales_diff = number_format($sales_diff, 2, ',', '');
												$periode = 'Vormonat';
                                             }
                                        ?>
                                        Veränderung zum {{ $periode }}: {{ $sales_diff }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            {{ Form::open(array('id' => 'switch-sales-count', 'route' => array('Dashboard.index', 0), 'method' => 'POST', 'files' => false)) }}
                                <input type="checkbox" id="switch-sales" name="switch-sales" data-render="switchery" data-theme="default" {{ $checked }}/>
                                <span>Umsatz monatlich</span>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6">
                    <div class="panel panel-inverse">
                        <div class="panel-heading"><h4 class="panel-title">Umsatz nach Produkttypen</h4></div>
                        <div class="panel-body">
                            <div id="contracts-legend" style="float: right; padding: 25px;">
                                <!-- Legende -->
                            </div>
                            <div id="sales-chart" style="width: 100%; height: 300px;">
                                <!-- Chart -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!--
            <script type="text/javascript">
                window.onload = function() {

                    // float bar chart -> sales
                    var data = <?php echo json_encode($chart_data_sales); ?>;
                    var options = {
                        xaxis: {
                            mode: 'categories',
                            tickLength: 0
                        },
                        yaxis: {
                            tickDecimals: 2
                        },
                        grid: { borderWidth: 0 },
                        bars: {
                            show: true,
                            align: "center",
                            barWidth: 0.5
                        }
                    };

                    $.plot($("#sales-chart"),
                        [ {data: data } ],
                        options
                    );
                }
            </script>
-->
            <br><br><br>
        @endif

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <h4 class="panel-title">Test Ion.RangeSlider</h4>
                    </div>
                    <div class="panel-body">
                        <input type="text" id="example_id" name="example_name" value="" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

<script type="text/javascript">
    window.onload = function() {
        // flot interactive chart -> contracts
        var valid_contracts = <?php echo json_encode($chart_data_valid_contracts); ?>;
        var invalid_contracts = <?php echo json_encode($chart_data_invalid_contracts); ?>;
        var options = {
            colors: ['green', 'red'],
            xaxis: { mode: 'categories' },
            yaxis: { tickDecimals: 0 },
            grid: { borderWidth: 0 },
            legend: {
                show: true,
                container: $('#contracts-legend')
            }
        };

        $.plot($("#contracts-chart"),
            [
                {label: "aktive Verträge", data: valid_contracts },
                {label: "inaktive Verträge", data: invalid_contracts }
            ],
            options);

        // float bar chart -> sales
        var data = <?php echo json_encode($chart_data_sales); ?>;
        var options = {
            xaxis: {
                mode: 'categories',
                tickLength: 0
            },
            yaxis: {
                tickDecimals: 2
            },
            grid: { borderWidth: 0 },
            bars: {
                show: true,
                align: "center",
                barWidth: 0.5
            }
        };

        $.plot($("#sales-chart"),
            [ {data: data } ],
            options
        );

        // float bar chart -> sales
        var data = <?php echo json_encode($chart_data_sales); ?>;
        var options = {
            xaxis: {
                mode: 'categories',
                tickLength: 0
            },
            yaxis: {
                tickDecimals: 2
            },
            grid: { borderWidth: 0 },
            bars: {
                show: true,
                align: "center",
                barWidth: 0.5
            }
        };

        $.plot($("#sales-chart"),
            [ {data: data } ],
            options
        );

        // range slider
        $("#example_id").ionRangeSlider();

        FormSliderSwitcher.init();

        $('#switch-sales').change(function() {
            $('#switch-sales-count').submit();
        })
    };
</script>