@extends ('Layout.split-nopanel')

<meta name="csrf-token" content="{{ \Session::get('_token') }}">

@section('content_top')

    {!! $headline !!}

@stop


@section ('content_left')

    @include ('Generic.logging')

    {{-- Error Message --}}
    <?php $blade_type = 'form' ?>
    @include('Generic.above_infos')

    {{-- Auto update SNMP values Button --}}
    <div class="row justify-content-end">
        @if ($netelement->controlling_link)
            {!! link_to($netelement->controlling_link, 'View...', ['class' => 'btn btn-primary mb-3']) !!}
        @endif
        <input id="stop-button" class="btn btn-primary mb-3 ml-5 mr-4" onclick="subscribe()" value="auto update">
    </div>

    {{-- PARAMETERS --}}
    @if (isset ($form_fields['list']))
        {!! Form::model($netelement, array('route' => array('NetElement.controlling_update', $netelement->id, $paramId, $index), 'method' => 'put', 'files' => true)) !!}

        {{-- LIST --}}
        @if ($form_fields['list'])
        <div class="col-md-12 row" style="padding-right: 0px;"><div class="col-md-12 well row">
        @foreach ($form_fields['list'] as $field)
            <div class="col-md-6">
            {!! $field !!}
            </div>
        @endforeach
        </div></div>
        @endif

        {{-- FRAMES --}}
        @if ($form_fields['frame']['linear'])
            <?php
                switch (count($form_fields['frame']['linear'])) {
                    case 1:
                        $col_width = 12; break;
                    case 2:
                    case 4:
                        $col_width = 6; break;
                    default:
                        $col_width = 4; break;
                }
            ?>
            <div class="col-md-12 row" style="padding-right: 0px;">
            @foreach ($form_fields['frame']['linear'] as $frame)
                <div class="col-md-{{$col_width}} well">
                    @foreach ($frame as $field)
                        {!! $field !!}
                    @endforeach
                </div>
            @endforeach
            </div>
        @endif

        @foreach ($form_fields['frame']['tabular'] as $row)
            <div class="col-md-12 row" style="padding-right: 0px;">
                <?php $col_width = (int) (12 / count($row)) ?>
                @foreach ($row as $col)
                    <div class="col-md-{{$col_width}} well">
                        @foreach ($col as $field)
                            {!! $field !!}
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach

        {{-- TABLES --}}
        @foreach ($form_fields['table'] as $table)
            <table class="table controllingtable table-bordered">
                <thead>
                        <th style="padding: 4px"> Index </th>
                    @foreach ($table['head'] as $oid => $head)
                        <th align="center" style="padding: 4px">{!!$head!!}</th>
                    @endforeach
                </thead>
                <tbody>
                    @foreach ($table['body'] as $i => $row)
                        <tr>
                            <?php $i = str_replace('.', '', $i) ?>
                            <td> {!! isset($table['3rd_dim']) ? HTML::linkRoute('NetElement.controlling_edit', $i, [$table['3rd_dim']['netelement_id'], $table['3rd_dim']['paramId'], $i]) : $i !!} </td>
                            @foreach ($row as $col)
                                <td align="center" style="padding: 4px"> {!! $col !!} </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach

        {{-- Save Button --}}
        <div class="d-flex justify-content-center">
            <input
                class="btn btn-primary"
                value="{!! \App\Http\Controllers\BaseViewController::translate_view($save_button_name , 'Button') !!}"
                type="submit">
        </div>

        {!! Form::close() !!}

    @endif

    {{-- Test output during development --}}
    @if (0)
        <div id="test">
            Test
            <input style="simple;width: 85px;" name=".1.2.3.3.3" type="text" value="0">
            <div style="display: contents" name=".1.23.4.5">0</div>
        </div>
    @endif

    {{-- javascript --}}
    @include('Generic.form-js')

@stop


@section('javascript_extra')

<script src="{{ asset('vendor/pusher-with-encryption.min.js') }}"></script>
<script language="javascript">

    channel = "{{ \Modules\HfcSnmp\Events\NewSnmpValues::getChannelName($netelement, $paramId, $index) }}";
    subscribed = false;

    function subscribe()
    {
        console.log('trigger SNMP query loop');
        subscribed = true;

        // Trigger SNMP polling and/or add subscriber
        window.xhrInit = $.ajax({
            url: "{{ route('NetElement.triggerSnmpQueryLoop', [$netelement->id, $paramId, $index]) }}",
            type: "post",
            data: {
                _token: "{{\Session::get('_token')}}",
            },
            success: function (msg) {
                console.log('SnmpQueryLoop ' + msg);
            },
        });

        console.log('Subscribe to channel ' + channel);

        // window.echo.channel(channel)
        window.echo.join(channel)
            .listen('.newSnmpValues', (data) => {
                // console.log((new Date()).toLocaleTimeString(), data);
                data = JSON.parse(data.data);

                for (var key in data) {
                    if (document.getElementsByName(key)[0] instanceof HTMLInputElement) {
                        document.getElementsByName(key)[0].value = data[key];
                    } else if (document.getElementsByName(key)[0] != undefined) {
                        document.getElementsByName(key)[0].innerHTML = data[key];
                    }
                }

            })
            .here((users) => {
                console.log('Listening to channel ' + channel);
            });

        $('#stop-button').val('stop');
        $('#stop-button').attr("onclick", "unsubscribe(true)");
    }

    /**
     * Unsubscribe from channel
     *
     * @param bool  function was triggered manually by klicking stop button, or automatically by closing/changing active tab
     */
    function unsubscribe(manually)
    {
        if (manually) {
            subscribed = false;
        }

        echo.leave(channel);
        console.log('Leave channel ' + channel);

        $('#stop-button').val('auto update');
        $('#stop-button').attr("onclick", "subscribe()");
    }

    // Only listen in active tab
    document.addEventListener("visibilitychange", function() {
        tabVisible = document.hidden ? 'hidden' : 'active';
        console.log('Tab visibility changed to ' + tabVisible);

        if (document.hidden) {
            unsubscribe(false);
        } else if (subscribed) {
            subscribe();
        }
    });

    $(document).ready(function()
    {
        if (Number("{{$reload}}")) {
            setTimeout(subscribe(), "{{$reload}}" * 1000);
        }

        $('.controllingtable').DataTable({
            dom: 'lBfrtip',
            @include('datatables.buttons')
            @include('datatables.lang')
        });
    });

    // this.source = new EventSource("{!! route('NetElement.triggerSnmpQueryLoop', [$netelement->id, $paramId, $index]) !!}");
    // this.source.onmessage = function(e) {
    //     console.log(JSON.parse(e.data));
    // }
    // this.source.close();

</script>

<script type="module">
    import Echo from "{{ asset('vendor/echo.js') }}";

    window.echo = new Echo({
        broadcaster: 'pusher',
        key: 'local',
        wsHost: window.location.hostname,
        wsPort: 6001,
        wssPort: 6001,
        forceTLS: true,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    });
</script>

@stop
