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
@extends ('Layout.default')

@section ('contracts_total')
    <h4>{{ trans('view.dashboard.contracts') }} {{ date('m/Y') }}</h4>
    <p>
        @if ($contracts_data && $contracts_data['total'])
            {{ $contracts_data['total'] }}
        @else
            {{ trans('view.dashboard.noContracts') }}
        @endif
    </p>
@stop

@section ('date')
    <h4>{{ trans('view.dashboard.date') }}</h4>
    <p>{{ date('d.m.Y') }}</p>
@stop

@section('content')

    <div class="col-md-12">

        <h1 class="page-header">{{ $title }}</h1>

        {{--Quickstart--}}

        <div class="row">
            @DivOpen(7)
            @include('provbase::widgets.quickstart')
            @DivClose()
            @DivOpen(2)
            @DivClose()

            @DivOpen(3)
            @include ('bootstrap.widget',
                array (
                    'content' => 'date',
                    'widget_icon' => 'calendar',
                    'widget_bg_color' => 'purple',
                )
            )
            @DivClose()
        </div>
    </div>
    <div class="col-md-12">
        <div class="row">
            @DivOpen(3)
            @include ('bootstrap.widget',
                array (
                   'content' => 'contracts_total',
                    'widget_icon' => 'users',
                    'widget_bg_color' => 'green',
                    'link_target' => '#anchor-contracts',
                )
            )
            @DivClose()
        </div>
        <div class="row">
            @DivOpen(4)
                <div class="widget widget-stats bg-blue">
                    {{-- info/data --}}
                    <div class="stats-info text-center">

                        {!! HTML::decode (HTML::linkRoute('Modem.firmware',
                            '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
                                <i style="font-size: 25px;" class="img-center fa fa-file-code-o p-10"></i><br>
                                <span class="text-ellipsis text-center">Firmwares</span>
                            </span>'))
                        !!}
                        {!! HTML::decode (HTML::linkRoute('Modem.cwmp',
                            '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
                                <i style="font-size: 25px;" class="img-center fa fa-hdd-o p-10"></i><br>
                                <span class="text-ellipsis text-center">CWMP</span>
                            </span>'))
                        !!}

                        {{-- reference link --}}
                        <div class="stats-link noHover"><a href="#"><br></a></div>

                    </div>
                </div>
            @DivClose()

            <div class="col-md-4">
                @include('Generic.widgets.moduleDocu', [ 'urls' => [
                    'documentation' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/Provisioning',
                    'youtube' => 'https://youtu.be/RjMlhKQXgU4',
                    'forum' => 'https://devel.roetzer-engineering.com/confluence/display/nmsprimeforum/Provisioning+General',
                ]])
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                @if (Module::collections()->has('HfcCustomer'))
                    @section('impaired_modems')
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" style="padding-top: 0; padding-bottom: 0;" for="impairedModemRow">{{ trans('messages.sort') }}</label>
                                <select class="custom-select" id="impairedModemRow">
                                    @foreach (config('hfcreq.hfParameters') as $value => $name)
                                        @if ($loop->first)
                                            <option selected value="{{ $value }}">{{ $name }}</option>
                                        @else
                                            <option value="{{ $value }}">{{ $name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group-prepend" style="margin-top: 20px; margin-bottom: 20px;">
                                <label class="input-group-text" style="padding-top: 0; padding-bottom: 0;" for="lowerValue">{{ trans('messages.minimum') }}</label>
                                <input type="number" value="50" class="form-control dark:bg-stone-700 dark:text-slate-100" id="lowerValue">
                            </div>
                            <div class="input-group-prepend">
                                <label class="input-group-text" style="padding-top: 0; padding-bottom: 0;" for="upperValue">{{ trans('messages.maximum') }}</label>
                                <input type="number" value="75" class="form-control dark:bg-stone-700 dark:text-slate-100" id="upperValue">
                            </div>
                        </div>
                        <button class="btn btn-primary" onclick="filterImpairedModems()" type="button">{{ trans('view.Button_Search') }}</button>
                        <a href="{!! route('CustomerTopo.show_impaired', ['offline']) !!}" class="btn btn-secondary" type="button" style="float: right;">{{ trans('messages.showOfflineModems') }}</a>
                    @stop
                    @include ('bootstrap.panel', [
                            'content' => "impaired_modems",
                            'view_header' => trans('view.dashboard.impairedModem'),
                            'height' => 'auto',
                            'i' => '1',
                        ])
                @endif
            </div>
        </div>
    </div>

@stop

@section('javascript_extra')
    @if (Module::collections()->has('HfcCustomer'))
    <script type="text/javascript">
        function filterImpairedModems() {
            let url = "{!! route('CustomerTopo.show_impaired', ['row' => 'impairedModemRow', 'lower' => 'lowerValue', 'upper' => 'upperValue']) !!}";

            ['impairedModemRow', 'lowerValue', 'upperValue'].forEach( function (attribute) {
                return url = url.replace(attribute, document.getElementById(attribute).value);
            });

            document.location.href = url;
        }
    </script>
    @endif
@endsection
