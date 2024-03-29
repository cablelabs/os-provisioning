<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
@extends ('Layout.split-nopanel')

<meta name="csrf-token" content="{{ \Session::get('_token') }}">

@section('content_top')

    {!! $headline !!}

@stop

@section ('content_left')

    @include ('Generic.logging')

    {{-- Error Messages --}}
    @php
        $blade_type = 'form';
    @endphp

    @include('Generic.above_infos')

    {{-- Auto update button - Show only when device could be queried via SNMP --}}
    @if (! $error)
        <div class="row justify-content-end items-baseline space-x-2">
            @if ($netelement->controlling_link)
                {!! link_to($netelement->controlling_link, 'View...', ['class' => 'btn btn-primary mb-3']) !!}
            @endif
            <div id="stop-button" class="btn mr-4 mb-1 border border-gray-800 btn-outline-dark" title="{{ trans('view.neControl.autoUpdate.stopped') }}" onclick="subscribe()">
                <i class="fa fa-refresh mr-0"></i>
            </div>
        </div>
    @endif

    {{-- PARAMETERS --}}
    @if (isset ($form_fields['list']))
        {!! Form::model($netelement, array('route' => array('NetElement.controlling_update', $netelement->id, $paramId, $index), 'method' => 'put', 'files' => true)) !!}

        {{-- LIST --}}
        @if ($form_fields['list'])
        <div class="col-md-12 row pr-0"><div class="col-md-12 well row">
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
            <div class="col-md-12 row pr-0">
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
            <div class="col-md-12 row pr-0">
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
                        <th class="p-1"> Index </th>
                    @foreach ($table['head'] as $oid => $head)
                        <th align="center" class="text-center p-1">{!!$head!!}</th>
                    @endforeach
                </thead>
                <tbody>
                    @foreach ($table['body'] as $i => $row)
                        <tr>
                            <?php
                                if ($i[0] == '.') {
                                    $i = substr($i, 1);
                                }
                            ?>
                            <td> {!! isset($table['3rd_dim']) ? HTML::linkRoute('NetElement.controlling_edit', $i, [$table['3rd_dim']['netelement_id'], $table['3rd_dim']['paramId'], $i]) : $i !!} </td>
                            @foreach ($row as $col)
                                <td align="center" class="p-1"> {!! $col !!} </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach

        {{-- Save Button --}}
        <div class="flex justify-content-center">
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
<script language="javascript">
    let channel = "{{ \Modules\HfcSnmp\Events\NewSnmpValues::getChannelName($netelement, $paramId, $index) }}"
    let subscribed = false
    wssConnect()

    function subscribe()
    {
        if (document.hidden) {
            return
        }

        console.log('trigger SNMP query loop')
        subscribed = true

        // Trigger SNMP polling and/or add subscriber
        axios.post('{{ route('NetElement.triggerSnmpQueryLoop', [$netelement->id, $paramId, $index]) }}')
            .then(function (msg) {
                console.log('SnmpQueryLoop ' + msg)
            })

        console.log('Subscribe to channel ' + channel)

        // window.echo.channel(channel)
        echo.join(channel)
            .listen('.newSnmpValues', (data) => {
                // console.log((new Date()).toLocaleTimeString(), data)
                data = JSON.parse(data.data)

                for (var key in data) {
                    if (document.getElementsByName(key)[0] instanceof HTMLInputElement) {
                        document.getElementsByName(key)[0].value = data[key]
                    } else if (document.getElementsByName(key)[0] != undefined) {
                        document.getElementsByName(key)[0].innerHTML = data[key]
                    }
                }

            })
            .here((users) => {
                console.log('Listening to channel ' + channel)
            })

        document.getElementById('stop-button').classList.remove('btn-outline-dark')
        document.getElementById('stop-button').classList.add('btn-dark')
        document.getElementById('stop-button').title = "{{ trans('view.neControl.autoUpdate.running') }}"
        document.getElementById('stop-button').onclick = () => unsubscribe(true)
    }

    /**
     * Unsubscribe from channel
     *
     * @param bool  function was triggered manually by klicking stop button, or automatically by closing/changing active tab
     */
    function unsubscribe(manually)
    {
        if (manually) {
            subscribed = false
        }

        echo.leave(channel)
        console.log('Leave channel ' + channel)

        document.getElementById('stop-button').classList.remove('btn-dark')
        document.getElementById('stop-button').classList.add('btn-outline-dark')
        document.getElementById('stop-button').title = "{{ trans('view.neControl.autoUpdate.stopped') }}"
        document.getElementById('stop-button').onclick = () => subscribe()
    }

    // Only listen in active tab
    document.addEventListener("visibilitychange", function() {
        tabVisible = document.hidden ? 'hidden' : 'active'
        console.log('Tab visibility changed to ' + tabVisible)

        if (document.hidden) {
            unsubscribe(false)
        } else if (subscribed) {
            subscribe()
        }
    })

    $(document).ready(function()
    {
        if ("{{ ! $error && $reload }}") {
            setTimeout(subscribe, "{{$reload}}" * 1000)
        }

        $('.controllingtable').DataTable({
            dom: 'lBfrtip',
            fixedHeader: true,
            @include('datatables.buttons')
            @include('datatables.lang')
        })
    })
</script>
@stop
