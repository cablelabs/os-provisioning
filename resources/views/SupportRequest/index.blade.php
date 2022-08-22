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

@section('content_left')

    <div class='col-md-10' style="padding: 15px">
    You currently don't have a valid SLA (service level agreement). If you already purchased it, please enter it to the Global Configuration:
    <a href="{{route('GlobalConfig.index')}}">SLA</a>.
    Otherwise the response time will be undefined.
    The following table gives you an overview over the response times:
    </div>

    <div class='col-md-8'>
        <div class="table">
            <table class="table m-b-0 table-bordered" width="60%">
            <thead>
                <tr class='info' align='center'>
                @foreach (['Problem', 'Time', 'Response Time with SLA', 'Response Time without SLA'] as $head)
                    <th align="center" style="padding: 4px">{!!$head!!}</th>
                @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach (\App\Sla::$conditions as $level => $times)
                    <tr align='center'>
                        <td class='active'> {!! $level !!} </td>
                        <td class='active'> {!! $times['time'] !!} </td>
                        <td> {!! $times['Response time'] !!} </td>
                        <td> {!! $times['RT without SLA'] !!} </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <div class='col-md-10' style="padding: 15px">
    You can just sign a contract or proceed without SLA.
    </div>

    <div class='row'>
        <div class='col-md-2 offset-sm-4'>
            <a class="btn btn-info btn-block" target='_blank' id='get_sla' href="https://www.nmsprime.com/calendar/">Contact Sales for Support Contract (SLA)</a>
        </div>
        <div class='col-md-2'>
            <a class="btn btn-secondary btn-block" id='request' href="{{route('SupportRequest.create')}}">{{\Session::has('clicked_sla') ? 'Request Support' : 'Get Help without SLA'}}</a>
        </div>
    </div>

@stop

@section('javascript')
    <script language="javascript">

        $('#get_sla').click(function (e) {

            $('#request').html('Request Support');

            /* push boolean variable to session on server */
            $.ajax({
                url: "{{route('Sla.clicked_sla')}}",
                type: "post",
                data: {
                    _token: "{{\Session::get('_token')}}",
                }
            });
        });

    </script>
@stop
