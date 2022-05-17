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

<template>
    <div id="realtimeValues">
        <div v-if="loading">
            <skeleton></skeleton>
        </div>
        <template v-else>
            @parent
            <div v-if="realtime != null" style="position: relative;">
                <div style="position: absolute; top: 1rem; right:1rem;">
                    <div v-if="status == 'running'">
                        <i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw success"></i>
                    </div>
                </div>
                @if ($modem->isTR069())
                    <button id="refreshRealtimeTr069" v-on:click="refreshRealtimeTr069" type="button" class="btn btn-info submit-button" style="margin-bottom: 5px;">
                        <i class="fa fa-refresh" aria-hidden="true"></i>
                    </button>
                @endif
                <div v-for="(table, tableName) in realtime">
                    <div v-if="! tableName.includes('Channel') && isNaN(tableName)">
                        <h4 v-if="isNaN(tableName)" class="d-flex">
                            <div v-text="tableName.replace(/^DT\_|^PT\_/g, '')"></div>
                            <div v-if="table.hasOwnProperty('#')" class="d-flex">
                                <div class="mx-2">-</div>
                                <div v-text="Object.keys(table['#']).length + ' ' + translations.modemAnalysis.channels">
                            </div>
                        </h4>
                        <div v-if="tableName.startsWith('DT_') || tableName.startsWith('PT_')" class="table-responsive" key="realtime-datatable">
                            <table :id="tableName" class="table streamtable table-bordered realtime-table" width="auto" :style="tableName.startsWith('PT_') ? 'min-height:650px;' : ''">
                                <thead>
                                    <tr class="active">
                                        <template v-for="(columnData, header) in table">
                                            <th v-text="translations.modemAnalysis.hasOwnProperty(header) ? translations.modemAnalysis[header] : header" class="text-center"></th>
                                        </template>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-for="(ifIndex, key) in table[Object.keys(table)[0]]">
                                        <tr>
                                            <template v-for="(columnData, header) in table">
                                                <td v-if="typeof columnData[ifIndex] == undefined || columnData[ifIndex] == null" class="text-center">
                                                    <p v-if="typeof ifIndex == undefined" v-text="n/a" style="margin: 0px auto; color: grey"></p>
                                                    <p v-else-if="columnData instanceof Array && columnData.length == 1" v-text="columnData[0]"></p>
                                                    <p v-else v-text="ifIndex"></p>
                                                </td>
                                                <td v-else-if="columnData[ifIndex] instanceof Array" class="text-center" :class="columnData[ifIndex][1]">
                                                    <p v-text="getCriticalValues(columnData[ifIndex], tableName, header)" style="margin: 0px auto; color: grey"></p>
                                                </td>
                                                <td v-else-if="typeof columnData[ifIndex] == 'string' && columnData[ifIndex].includes('OFDM')" class="text-center" style="cursor: pointer;">
                                                    <p v-text="columnData[ifIndex]" style="margin: 0px auto; color: grey"></p>
                                                    <span class="badge badge-pill badge-info" data-toggle="modal" data-target="#showOfdmDetails" v-on:click="setCurrentOfdmChannel(ofdmTableValues, tableName, ifIndex)">Details</span>
                                                    @include ('provmon::docsis31Modal')
                                                </td>
                                                <td v-else class="text-center">
                                                    <p v-text="columnData[ifIndex]" style="margin: 0px auto; color: grey"></p>
                                                </td>
                                            </template>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div v-else key="realtime-datatable">
                            <table class="table" style="width: auto; margin-bottom: 0px;">
                                <tr v-for="(row, rowName) in table">
                                    <th v-text="rowName" width="15%"></th>
                                    <td v-for="(line, lineName) in row">
                                        <p v-html="line" style="color: grey; margin-bottom: 0px;"></p>
                                    </td>
                                </tr>
                                <div v-if="tableName == Object.keys(realtime)[0]" style="float: right;">
                                    @if ($picture == 'images/modems/default.webp')
                                        <a href="https://github.com/nmsprime/nmsprime/issues/882">
                                            <img style="max-height: 150px; max-width: 200px; margin-top: 50px; display: block;" src="{{ url($picture) }}"></img>
                                        </a>
                                        <i style="float: right;" class="fa fa-2x p-t-5 fa-question-circle text-info" title="{{ trans('messages.contribute_modem_picture') }}"></i>
                                        <p style="color:red;">{{ trans('messages.no_modem_picture') }}</p>
                                    @else
                                        <img style="max-height: 150px; max-width: 200px; margin-top: 50px; display: block;" src="{{ url($picture) }}"></img>
                                    @endif
                                </div>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="alert alert-danger fade-show">{{trans('messages.modem_offline')}}</div>
                @if ($picture == 'images/modems/default.webp')
                    <div style="text-align: center">
                        <a href="https://github.com/nmsprime/nmsprime/issues/882" style="vertical-align: middle;">
                            <img style="max-height: 300px; max-width: 300px; margin: auto; display: inline;" src="{{ url($picture) }}"></img>
                        </a>
                    </div>
                    <i style="float: right;" class="fa fa-2x p-t-5 fa-question-circle text-info" title="{{ trans('messages.contribute_modem_picture') }}"></i>
                    <p style="color:red; margin-left: auto; margin-right: auto;">{{ trans('messages.no_modem_picture') }}</p>
                @else
                    <img style="max-height: 300px; max-width: 300px; margin: auto; display: block;" src="{{ url($picture) }}"></img>
                @endif
            </div>
        </template>
    </div>
</template>


<script>
export default {
            data () {
                return {
                    route: "{!! route('ProvMon.realtimeBroadcasting', ['id' => $modem->id]) !!}",
                    realtime: null,
                    status: null,
                    loading: true,
                    criticalValues: [],
                    docsis31Values: {},
                    currentOfdmTableValues: {},
                    currentTableName: null,
                    wsConnection: window.echo.connector.pusher.connection.state,
                    pingStarted: false,
                    selectedPing: 1,
                    floodPingResult: null,
                    selectedTask: null,
                    refreshObject: null,
                    isForm: false,
                    getWlanSettings: {
                        index: null,
                        channel: null,
                        ssid: null,
                        password: null,
                    },
                    getDnsSettings: {
                        dns: null,
                    },
                    taskOptions: @json($genieCmds),
                    pingOptions: null,
                    translations: {
                        entries: "{{ trans('messages.Entries') }}",
                        modemAnalysis: @json(trans('view.modemAnalysis')),
                    }
                }
            },
            mounted() {
                this.joinSocket()

                this.selectedTask = this.taskOptions.length ? this.taskOptions[0].task : null
                this.pingOptions = [
                    {id: 1, name: this.translations.modemAnalysis.floodping.lowLoad},
                    {id: 2, name: this.translations.modemAnalysis.floodping.averageLoad},
                    {id: 3, name: this.translations.modemAnalysis.floodping.bigLoad},
                    {id: 4, name: this.translations.modemAnalysis.floodping.hugeLoad},
                ]

                axios.post(this.route)
            },
            methods: {
                joinSocket: function() {
                    echo.join(channel)
                        .listen('.newRealtimeValues', (data) => {
                            this.realtime = data.data.length != 0 ? JSON.parse(data.data) : null
                            this.status = data.status
                            this.loading = false
                            this.initDatatables()
                        })
                        .here((users) => {
                            setTimeout(() => {
                                this.loading = false
                            }, 30000)
                        })
                },
                getCriticalValues: function(rowData, tableName, header) {
                    var name = tableName.replace(/^DT\_/g, '') + ' ' + header
                    if (rowData[1] == 'danger' && this.criticalValues.indexOf(name) == -1) {
                        this.criticalValues.push(name)
                    }

                    return rowData[0]
                },
                initDatatables: function() {
                    if (! this.realtime) {
                        return
                    }

                    Object.keys(this.realtime).forEach((tableName) => {
                        if (tableName.startsWith('PT_')) {
                            $( document ).ready(function() {
                                let dt_paginated = $(`#${tableName}`)
                                dt_paginated.DataTable().destroy()
                                dt_paginated.dataTable({
                                    order: [[0, 'desc']],
                                    pageLength: 10,
                                    language: i18nDT.language,
                                })
                            })
                        }
                    })
                },
                setTask: function (value) {
                    this.selectedTask = value
                    if (this.selectedTask == 'custom/setWlan' || this.selectedTask == 'custom/setDns') {
                        this.isForm = true

                        return
                    }

                    this.isForm = false
                },
                setTr069ParamsFromForm: function (task) {
                    const { ssid, password, ...withoutValues } = task
                    if (Object.values(withoutValues).some(x => (x === null || x == ''))) {
                        return this.$snotify.error(this.translations.modemAnalysis.missingInput)
                    }

                    this.$snotify.success(this.translations.modemAnalysis.refreshInProgress)
                    axios({
                        method: 'post',
                        url: '{{ route('Modem.genieTask', $modem->id) }}',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                        data: {
                            id: {{ $modem->id }},
                            taskName: this.selectedTask,
                            task: task
                        }
                    })
                    .then((response) => {
                        this.$snotify.info(response.data)
                    })
                    .catch((error) => {
                        console.error(error)
                        this.$snotify.error(error.message)
                    })
                },
                refreshGenieObject: function () {
                    this.$snotify.success(this.translations.modemAnalysis.refreshInProgress)

                    let needsRefresh = 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.*'
                    if (this.refreshObject && this.refreshObject == 'lan') {
                        needsRefresh = 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.*'
                    }

                    axios({
                        method:'POST',
                        url:'{{ route('Modem.refreshGenieObject', $modem->id) }}',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                        data: {
                            id: {{ $modem->id }},
                            object: needsRefresh,
                        }
                    })
                    .then((response) => {
                        this.$snotify.info(response.data)
                        location.reload()
                    })
                    .catch((error) => {
                        console.error(error)
                        this.$snotify.error(error.message)
                    })
                },
                refreshRealtimeTr069: function () {
                    this.$snotify.success(this.translations.modemAnalysis.refreshInProgress)

                    axios({
                        method:'POST',
                        url:'{{ route('ProvMon.refreshRealtimeTr069', $modem->id) }}',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                        data: {
                            id: {{ $modem->id }},
                        }
                    })
                    .then((response) => {
                        this.$snotify.info(response.data)
                    })
                    .catch((error) => {
                        console.error(error)
                        this.$snotify.error(error.message)
                    })
                },
                setWlan: function () {
                    this.setTr069ParamsFromForm(this.getWlanSettings)
                },
                setDns: function () {
                    this.setTr069ParamsFromForm(this.getDnsSettings)
                },
                setPing: function (value) {
                    this.selectedPing = value
                },
                updateGenieTasks: function() {
                    axios({
                        method: 'post',
                        url: '{{ route('Modem.genieTask', $modem->id) }}',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                        data: {
                            id: {{ $modem->id }},
                            task: this.selectedTask
                        }
                    })
                    .then((response) => {
                        this.taskOptions.forEach((option) => {
                            if (option.task == this.selectedTask && this.selectedTask.includes('task')) {
                                this.taskOptions.pop()
                            }
                        })

                        this.$snotify.success(response.data)
                    })
                    .catch((error) => {
                        console.error(error)
                        this.$snotify.error(error.message)
                    })
                },
                floodPing: function() {
                    let timeout = {
                        1: 5000,
                        2: 10000,
                        3: 30000,
                        4: 30000
                    }

                    this.$snotify.success('{{ trans('provmon::messages.analysis.pingInProgress') }}', null, {timeout: timeout[this.selectedPing]})
                    this.pingStarted = true
                    this.floodPingResult = ''

                    axios({
                        method: 'post',
                        url: '{{ route("Modem.floodPing", ["modem" => $modem->id]) }}',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                        data: {
                            task: this.selectedPing
                        }
                    })
                    .then((response) => {
                        this.floodPingResult = response.data
                        this.pingStarted = false
                    })
                    .catch((error) => {
                        console.error(error)
                        this.pingStarted = false
                    })
                },
                showSpectrum: function() {
                    var info = this.$snotify.info("{{ trans('messages.pleaseWait') }}")

                    axios({
                        method: 'GET',
                        url: '{{ route('ProvMon.createSpectrum', [$modem->id]) }}',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        contentType: 'json',
                        data: { _token: "{{ csrf_token() }}", }
                    })
                    .then((response) => {
                        if (! response.data || response.data == 'processing') {
                            response.data ? this.$snotify.success("{{ trans('provmon::messages.spectrum.processing') }}") :
                                this.$snotify.error("{{ trans('messages.noSpectrum') }}")

                            return
                        }

                        makeSpectrum(response.data.amplitudes, response.data.span)
                    })
                    .catch((error) => {
                        //Snotify.remove(info.id)
                        this.$snotify.error("{{ trans('messages.noSpectrum') }}")
                    })
                },
                setCurrentOfdmChannel: function(ofdmTableValues, tableName, ifIndex) {
                    this.currentTableName = tableName
                    if (tableName.includes('Downstream')) {
                        this.currentOfdmTableValues = ofdmTableValues[ifIndex]
                    } else {
                        this.currentOfdmTableValues = {...ofdmTableValues['IUC Stats'], ...ofdmTableValues[ifIndex]}
                    }
                },
                humanFileSize: function (size) {
                    let i = size == 0 ? 0 : Math.floor( Math.log(size) / Math.log(1024) )
                    return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['', 'k', 'M', 'G', 'T'][i]
                }
            },
            computed: {
                ofdmTableValues: function() {
                    ofdmTable = {}
                    let realtime = this.realtime
                    if (typeof realtime != undefined && realtime) {
                        Object.keys(realtime).forEach(function(channel) {
                            // get ifIndex of OFDM Channel
                            if (! isNaN(channel)) {
                                ofdmTable[channel] = {}
                                Object.keys(realtime[channel]).forEach(function(channelStats) {
                                    ofdmTable[channel][channelStats] = realtime[channel][channelStats]
                                })
                            } else if (channel.includes('IUC Stats')) {
                                ofdmTable['IUC Stats'] = {}
                                ofdmTable['IUC Stats'][channel] = realtime[channel]
                            }
                        })
                    }

                    return ofdmTable
                }
            }
        })
    }
</script>
