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
@extends ('provbase::layouts.split')

@section('content_dash')
    <div class="relative flex">
        <div class="absolute right-0 top-0">
            @include('Generic.documentation', ['documentation' => $modem->help])
        </div>
        @if ($dash)
            <div class="{{ count($dash) == 1 ? 'col-sm-10 col-xl-11 order-2' : '' }} ">
                @foreach ($dash as $key => $info)
                    @if (! $info)
                        @continue
                    @endif
                    <div class="alert alert-{{ $info['bsclass'] }} fade show">
                        <div>
                            {{ $info['text'] }}
                        </div>
                        @if (isset($info['instructions']))
                            <div class="m-t-10 m-b-5">
                                <code class="p-5">{{ $info['instructions'] }}</code>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@stop

@section('content_ping')
    <div class="tab-content min-h-[20rem] p-3">
        <div class="tab-pane" id="ping-test">
            @if ($online)
                <div class="font-semibold text-green-600 pb-2"><b>Modem is Online</b></div>
            @else
                <div class="font-semibold text-red-600">{{ trans('messages.modem_offline') }}</div>
            @endif
            {{-- pings are appended dynamically here by javascript --}}
        </div>

        <div class="tab-pane fade in" id="flood-ping">
            <form v-on:submit.prevent="floodPing">
                <div class="row flex">
                    <div class="flex-1">
                        <select2 id="pingselect" v-model="selectedPing" :initial="1" :i18n="{ all: '{{ trans('messages.all') }}'}">
                            <option v-for="option in pingOptions" :key="option.id" :value="option.id" v-text="option.text" :selected="option.id == 1"></option>
                        </select2>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary ml-3 mb-3" type="submit">{{ trans('view.modemAnalysis.sendPing') }}</button>
                    </div>
                </div>
            </form>
            {{-- Result --}}
            <div v-if="pingStarted && ! floodPingResult" class="flex justify-content-center m-t-20" style="position:relative;height:200px;">
                <div id="loader" style="position: absolute;"></div>
            </div>
            <div v-if="floodPingResult">
                <table>
                    <tr v-for="line in floodPingResult">
                        <td>
                            <p style="color: grey" v-text="line"></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @yield('arris_iperf')
    </div>
@stop

@section('content_log')
    <div class="tab-content">
        @include('provbase::Modem.logLeaseConfTabs')
        @include('provbase::Modem.radiusTabs')
    </div>
@stop

@section('content_realtime')
    @if (array_key_exists('DT_Current Session', $radius))
        <h4 class="h4"> {{ trans('view.modemAnalysis.currentSession') }} </h4>

        <div class="table-responsive">
            <table class="table streamtable table-bordered radius-table" width="auto">
                <thead>
                    @foreach ($radius['DT_Current Session'] as $colHeader => $colData)
                        <th class="active text-center">{{ $colHeader }}</th>
                    @endforeach
                </thead>
                <tbody>
                    @foreach ($radius['DT_Current Session'] as $colHeader => $colData)
                        <td class="text-center">{{ $colData[0] }}</td>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@stop

@if (Module::collections()->has('HfcCustomer'))
    @section('content_proximity_search')

        {!! Form::open(array('route' => 'CustomerTopo.show_prox', 'method' => 'GET')) !!}
        <div class="row">
        {!! Form::hidden('id', $modem->id) !!}
        {!! Form::number('radius', '1000') !!}
        {!! Form::submit(trans('view.Button_Search'), ['style' => 'simple']) !!}
        {!! Form::label('radius', 'Radius / m', ['class' => 'col-md-2 control-label']) !!}
        </div>
        {!! Form::close() !!}

    @stop
@endif

@section ('javascript')
<script type="text/javascript">

@if ($ip)
    $(document).ready(function() {
        setTimeout(function() {
            var source = new EventSource(" {{ route('Modem.realtimePing', $ip) }}");

            source.onmessage = function(e) {
                // close connection
                if (e.data == 'finished')
                {
                    source.close();
                    return;
                }

                document.getElementById('ping-test').innerHTML += e.data;
            }
        }, 500);

        $('table.radius-table').DataTable({
            autoWidth: true,
            paging: false,
            info: true,
            searching: false,
            fixedHeader: true,
        });
    });
@endif
</script>

@include('Generic.handlePanel')

@stop
