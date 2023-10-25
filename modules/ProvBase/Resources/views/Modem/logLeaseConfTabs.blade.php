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
<div v-if="log" class="tab-pane fade in flex" id="log">
    <div class="flex justify-between">
        <span class="text-green-600">
            <b>Modem Logfile</b>
        </span>
        <button v-if="log.dhcp && log.tr069" class="btn btn-primary ml-2 mb-2" v-on:click="switchLog" v-text="switchLogButtonText"></button>
    </div>
    <br>
    <div v-for="line in log[currentLog]">
        <table>
            <tr>
                <td>
                    <span color="grey" v-text="line"></span>
                </td>
            </tr>
        </table>
    </div>
</div>
<div v-else>
    <span class="text-red-600">{{ trans('messages.modem_log_error') }}</span>
</div>

<div class="tab-pane fade in" id="lease">
    @if ($lease)
        <span class="{{ $lease['state'] }}"><b>{{ $lease['forecast'] }}</b></span><br>
        @foreach ($lease['text'] as $line)
            <table>
                <tr>
                    <td>
                        <span color="grey">{!!$line!!}</span>
                    </td>
                </tr>
            </table>
        @endforeach
    @else
        <span class="text-red-600">{{ trans('messages.modem_lease_error')}}</span>
    @endif
</div>
<div class="tab-pane fade in" id="configfile">
    @if ($configfile)
        @if ($modem->configfile->device != 'tr069')
            <span class="text-green-600"><b>Modem Configfile ({{$configfile['mtime']}})</b></span><br>
            @if (isset($configfile['warn']))
                <span class="text-red-600"><b>{{$configfile['warn']}}</b></span><br>
            @endif
        @else
            <?php
                $blade_type = 'form';
            ?>
            @include('Generic.above_infos')
            <form v-if="taskOptions" v-on:submit.prevent="updateGenieTasks">
                <div class="row flex">
                    <div style="flex: 1;">
                        <select2 v-model="selectedTask" :initial="taskOptions.length > 0 ? taskOptions[0] : ''" v-on:input="setTask" :as-array="true" :i18n="{ all: '{{ trans('messages.all') }}'}">
                            <option v-for="(option, i) in taskOptions" :key="i" :value="option.task" v-text="option.name"></option>
                        </select2>
                    </div>
                    <button v-if="! isForm" type="submit" class="btn btn-danger" style="margin-left: 10px; margin-bottom: 10px;">{{ trans('view.Button_Submit') }}</button>
                </div>
            </form>
        @endif
        <div v-if="selectedTask == 'custom/setWlan'">
            <form v-on:submit.prevent="setWlan" style="margin-top: 10px;">
                <div class="form-group row">
                    <label for="WLANIndex" class="col-sm-2 col-form-label" style="display: flex; align-items: center;">{{ trans('view.modemAnalysis.index') }}</label>
                    <div class="col-sm-10">
                        <input v-model="getWlanSettings['index']" type="number" class="form-control" id="WLANIndex">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Channel" class="col-sm-2 col-form-label" style="display: flex; align-items: center;">{{ trans('view.modemAnalysis.channel') }}</label>
                    <div class="col-sm-10">
                        <input v-model="getWlanSettings['channel']" type="number" class="form-control" id="Channel" placeholder="{{ trans('view.modemAnalysis.wlanChannelInfo') }}" title="{{ trans('view.modemAnalysis.wlanChannelInfo') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="SSID" class="col-sm-2 col-form-label" style="display: flex; align-items: center;">SSID</label>
                    <div class="col-sm-10">
                        <input v-model="getWlanSettings['ssid']" type="text" class="form-control" id="SSID" placeholder="SSID">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Password" class="col-sm-2 col-form-label" style="display: flex; align-items: center;">{{ trans('messages.Password') }}</label>
                    <div class="col-sm-10">
                        <input v-model="getWlanSettings['password']" type="password" class="form-control" id="Password" placeholder="{{ trans('messages.Password') }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-danger" style="margin-top: 5px; margin-bottom: 10px;">{{ trans('view.Button_Submit') }}</button>
            </form>
        </div>
        <div v-if="selectedTask == 'custom/setDns'">
            <form v-on:submit.prevent="setDns" style="margin-top: 10px;">
                <div class="form-group row">
                    <label for="DNS" class="col-sm-2 col-form-label" style="display: flex; align-items: center;">DNS</label>
                    <div class="col-sm-10">
                        <input v-model="getDnsSettings['dns']" type="text" class="form-control" id="DNS" placeholder="0.0.0.0,0.0.0.0">
                    </div>
                </div>
                <button type="submit" class="btn btn-danger" style="margin-top: 5px; margin-bottom: 10px;">{{ trans('view.Button_Submit') }}</button>
            </form>
        </div>

        @foreach ($configfile['text'] as $line)
            <table>
                <tr>
                    <td>
                     <span color="grey">{!! $line !!}</span>
                    </td>
                </tr>
            </table>
        @endforeach
    @else
        <span class="text-red-600">{{ trans('messages.modem_configfile_error')}}</span>
    @endif
</div>

<div class="tab-pane fade in" id="eventlog">
    @if ($eventlog)
        <div class="table-responsive">
            <table class="table streamtable table-bordered" width="100%">
                <thead>
                    <tr class='active'>
                        @foreach (array_shift($eventlog) as $col_name)
                            <th class='text-center'>{{$col_name}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach ($eventlog as $row)
                    <tr class = "{{$row[2]}}">
                        @foreach ($row as $idx => $data)
                            @if($idx != 2)
                                <td><span>{{$data}}</span></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <span class="text-red-600">{{ trans('messages.modem_eventlog_error')}}</span>
    @endif
</div>

@foreach (['wifi' => $wifi, 'lan' => $lan] as $tab => $configInterface)
    <div class="tab-pane fade in" id="{{ $tab }}">
        @if ($configInterface && is_array($configInterface))
            <button id="{{ 'refresh'.ucfirst($tab) }}" v-on:click="refreshGenieObject('{{ $tab }}')" type="button" class="btn btn-info submit-button" style="margin-bottom: 5px;">
                <i class="fa fa-refresh" aria-hidden="true"></i>
            </button>
            <table class="table streamtable table-bordered">
                <thead>
                    <tr class="active">
                        <th class="text-center" style="min-width: 20px;">{{ trans('view.modemAnalysis.index') }}</th>
                        @foreach ($configInterface[array_key_first($configInterface)] as $name => $value)
                            <th class="text-center">{{ $name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($configInterface as $entry => $config)
                        <tr>
                            <td>{{ $entry }}</td>
                            @foreach ($config as $name => $value)
                                <td class="text-center">
                                    <p style="color: grey; margin-bottom: 0px;">{{ $value }}</p>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endforeach
