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
@extends ('provbase::layouts.cpe_mta_split')


@section('content_dash')
    <div class="btn pull-right">
        @include('Generic.documentation', ['documentation' => $modem->help])
    </div>

    @if ($dash)
        <font color="grey">{!!$dash!!}</font>
    @endif
@stop

@section('content_lease')

    @if ($lease)
        @if (isset($lease['ipv6']))
            <h4> IPv4 </h4>
        @endif
        <font color="{{$lease['state']}}"><b>{!!$lease['forecast']!!}</b></font><br>
        <table>
            @foreach ($lease['text'] as $line)
                <tr><td><font color="grey">{!!$line!!}</font></td></tr>
            @endforeach
        </table>

        <br>

        @if (isset($lease['ipv6']))
            <h4> IPv6 </h4>
            <div class="table-responsive">
            <table class="table streamtable table-bordered" width="auto">
                <thead>
                    <tr class="active">
                        <th width="20px"></th>
                        <th class="text-center">#</th>
                        <th class="text-center">MAC</th>
                        <th class="text-center">{{ trans('messages.Address') }}</th>
                        <th class="text-center">{{ trans('messages.prefix') }}</th>
                        <th class="text-center">{{ trans('provbase::view.dhcp.lifetime') }}</th>
                        <th class="text-center">{{ trans('provbase::view.dhcp.expiration') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lease['ipv6'] as $key => $lease6)
                    <tr class="{{ $lease6->bsclass }}">
                        {{-- <td class="text-center"><font color="grey">{{}}</font></td> --}}
                        <td class="text-center"></td>
                        <td class="text-center">{{ $key }}</td>
                        <td class="text-center">{{ $lease6->hwaddr }}</td>
                        <td class="text-center">{{ $lease6->address }}</td>
                        <td class="text-center">{{ $lease6->prefix_len }}</td>
                        <td class="text-center">{{ $lease6->valid_lifetime }}</td>
                        <td class="text-center">{{ $lease6->expire }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endif
    @else
        <font color="red">{{ trans('messages.modem_lease_error')}}</font>
    @endif

@stop

@section('content_log')
    @if ($log)
        <font color="green"><b>{{$type}} Logs</b></font><br>
        <table>
            @foreach ($log as $line)
                <tr><td><font color="grey">{{$line}}</font></td></tr>
            @endforeach
        </table>
    @else
        <font color="red">{{$type.' '.trans('messages.cpe_log_error')}}</font>
    @endif
@stop

@section('content_configfile')
    @if (isset($configfile))
        <font color="green"><b>{{$type}} Configfile ({{$configfile['mtime']}})</b></font><br>
        @if (isset($configfile['warn']))
            <font color="red"><b>{{$configfile['warn']}}</b></font><br>
        @endif
        <table>
            @foreach ($configfile['text'] as $line)
                <tr><td><font color="grey">{!!$line!!}</font></td></tr>
            @endforeach
        </table>
    @else
        <font color="red">{{ trans('messages.mta_configfile_error')}}</font>
    @endif
@stop

@section('content_ping')

    @if ($ping)
        <font color="{{ isset($ping[1]) ? "success" : "warning" }}">
            <b>{{ isset($ping[1]) ? "$type is Online" : trans('messages.device_probably_online', ['type' => $type]) }}</b>
        </font>
        <br>
        <table>
            @foreach ($ping as $line)
                <tr><td><font color="grey">{{$line}}</font></td></tr>
            @endforeach
        </table>
    @else
        <font color="red">{{$type}} is Offline</font> <br>
    @endif

@stop

@section('javascript')

    @include('Generic.handlePanel')

    <script language="javascript">

        $(document).ready(function() {
            $('table.streamtable').DataTable({
                {{-- Translate Datatables Base --}}
                @include('datatables.lang')
                responsive: {
                    details: {
                        type: 'column' {{-- auto resize the Table to fit the viewing device --}}
                    }
                },
                fixedHeader: true,
                autoWidth: true,
                paging: false,
                info: false,
                searching: false,
                aoColumnDefs: [ {
                    className: 'control',
                    orderable: false,
                    searchable: false,
                    targets:   [0]
                } ]
            });
        });

    </script>

@stop
