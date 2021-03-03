<div class="tab-pane fade in" id="log">
    @if ($log)
        <font color="green"><b>Modem Logfile</b></font><br>
        @foreach ($log as $line)
            <table>
                <tr>
                    <td>
                     <font color="grey">{{$line}}</font>
                    </td>
                </tr>
            </table>
        @endforeach
    @else
        <font color="red">{{ trans('messages.modem_log_error') }}</font>
    @endif
</div>
<div class="tab-pane fade in" id="lease">
    @if ($lease)
        <font color="{{$lease['state']}}"><b>{{$lease['forecast']}}</b></font><br>
        @foreach ($lease['text'] as $line)
            <table>
                <tr>
                    <td>
                        <font color="grey">{!!$line!!}</font>
                    </td>
                </tr>
            </table>
        @endforeach
    @else
        <font color="red">{{ trans('messages.modem_lease_error')}}</font>
    @endif
</div>
<div class="tab-pane fade in" id="configfile">
    @if ($configfile)
        @if ($modem->configfile->device != 'tr069')
            <font color="green"><b>Modem Configfile ({{$configfile['mtime']}})</b></font><br>
            @if (isset($configfile['warn']))
                <font color="red"><b>{{$configfile['warn']}}</b></font><br>
            @endif
        @else
            <?php
                $blade_type = 'form';
            ?>
            @include('Generic.above_infos')

            {!! Form::open(array('route' => ['Modem.genieTask', $modem->id], 'method' => 'POST')) !!}
            <div class="row">
            {!! Form::select('task', $genieCmds) !!}
            {!! Form::submit(trans('view.Button_Submit'), ['style' => 'simple', 'class' => 'btn-danger']) !!}
            </div>
            {!! Form::close() !!}
        @endif
        @foreach ($configfile['text'] as $line)
            <table>
                <tr>
                    <td>
                     <font color="grey">{!! $line !!}</font>
                    </td>
                </tr>
            </table>
        @endforeach
    @else
        <font color="red">{{ trans('messages.modem_configfile_error')}}</font>
    @endif
</div>

<div class="tab-pane fade in" id="eventlog">
    @if ($eventlog)
        <div class="table-responsive">
            <table class="table streamtable table-bordered" width="100%">
                <thead>
                    <tr class='active'>
                        <th width="20px"></th>
                        @foreach (array_shift($eventlog) as $col_name)
                            <th class='text-center'>{{$col_name}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach ($eventlog as $row)
                    <tr class = "{{$row[2]}}">
                        <td></td>
                        @foreach ($row as $idx => $data)
                            @if($idx != 2)
                                <td><font>{{$data}}</font></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <font color="red">{{ trans('messages.modem_eventlog_error')}}</font>
    @endif
</div>
