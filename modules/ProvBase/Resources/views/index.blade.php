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
@extends ('Generic.dashboard')

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

@section('dashboard')
    {{--Quickstart--}}
    <div class="grid {{ $gap }} sm:grid-cols-12">
        <div class="col-span-12 2xl:col-span-10">
            @include('provbase::widgets.quickstart')
        </div>
        <div class="sm:col-span-6 lg:col-span-5 2xl:col-span-2">
            @include ('bootstrap.widget', [
                'content' => 'date',
                'widget_icon' => 'fa-calendar',
                'widget_bg_color' => 'bg-violet-400',
            ])
        </div>
        <div class="sm:col-span-6 lg:col-span-5 2xl:col-span-3">
            @include ('bootstrap.widget', [
            'content' => 'contracts_total',
                'widget_icon' => 'fa-users',
                'widget_bg_color' => 'bg-lime-500/75',
                'link_target' => '#anchor-contracts',
            ])
        </div>
        <div class="bg-blue-400 sm:col-span-6 lg:col-span-4 2xl:col-span-3 widget widget-stats">
            {{-- info/data --}}
            <div class="text-center stats-info">
                {!! HTML::decode (HTML::linkRoute('Modem.firmware',
                    '<span class="p-10 m-5 text-center btn btn-dark m-r-10">
                        <i style="font-size: 25px;" class="p-10 img-center fa fa-file-code-o"></i><br>
                        <span class="text-center text-ellipsis">Firmwares</span>
                    </span>'))
                !!}
                {!! HTML::decode (HTML::linkRoute('Modem.cwmp',
                    '<span class="p-10 m-5 text-center btn btn-dark m-r-10">
                        <i style="font-size: 25px;" class="p-10 img-center fa fa-hdd-o"></i><br>
                        <span class="text-center text-ellipsis">CWMP</span>
                    </span>'))
                !!}

                {{-- reference link --}}
                <div class="stats-link noHover"><a href="#"><br></a></div>
            </div>
        </div>
        <div class="sm:col-span-12 lg:col-span-8 2xl:col-span-6">
            @include('Generic.widgets.moduleDocu', [ 'urls' => [
                'documentation' => 'https://devel.nmsprime.com/confluence/display/NMS/Provisioning',
                'youtube' => 'https://youtu.be/RjMlhKQXgU4',
                'forum' => 'https://devel.nmsprime.com/confluence/display/nmsprimeforum/Provisioning+General',
            ]])
        </div>
        @if (Module::collections()->has('HfcCustomer'))
            <div class="sm:col-span-12 lg:col-span-6 2xl:col-span-4">
                @section('impaired_modems')
                    <div class="mb-3 input-group">
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
                            <input type="number" value="50" class="form-control dark:bg-slate-800 dark:text-slate-100" id="lowerValue">
                        </div>
                        <div class="input-group-prepend">
                            <label class="input-group-text" style="padding-top: 0; padding-bottom: 0;" for="upperValue">{{ trans('messages.maximum') }}</label>
                            <input type="number" value="75" class="form-control dark:bg-slate-800 dark:text-slate-100" id="upperValue">
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <button class="btn btn-primary" onclick="filterImpairedModems()" type="button">{{ trans('view.Button_Search') }}</button>
                        <a href="{!! route('CustomerTopo.show_impaired', ['offline']) !!}" class="btn btn-secondary">{{ trans('messages.showOfflineModems') }}</a>
                    </div>
                @stop
                @include ('bootstrap.panel', [
                        'content' => "impaired_modems",
                        'view_header' => trans('view.dashboard.impairedModem'),
                        'height' => 'auto',
                        'i' => '1',
                    ])
            </div>
        @endif
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
