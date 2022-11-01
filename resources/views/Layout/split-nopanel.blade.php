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

@php
    // actually this should be named contentMid now as there are 4 sections from left to right
    // where you can place content (leftLeft, left, right, rightRight)
    $leftMdSizeXl = $leftMdSizeLg = 12;
    $flex = '';

    if (! empty($__env->yieldContent('contentLeftLeft'))) {
        $leftMdSizeXl -= $mdSizes['leftLeftXl'];
        $leftMdSizeLg -= $mdSizes['leftLeftLg'];
        $flex = 'flex:1;';
    }

    if (! empty($__env->yieldContent('contentRightRight'))) {
        $leftMdSizeXl -= $mdSizes['rightRightXl'];
        $leftMdSizeLg -= $mdSizes['rightRightLg'];
    }

    $fullscreenRoutes = [
        'TreeErd.show',
        'TreeTopo.show',
        'CustomerModem.showDiagrams',
        'CustomerRect.show',
        'CustomerPoly.show',
        'CustomerTopo.show',
        'CustomerTopo.show_impaired',
        'CustomerModem.showModems',
        'CustomerTopo.show_prox',
        'VicinityGraph.show',
        'CoreMon.net.overview',
        'CoreMon.topology'
    ];

    if (in_array($routeName = request()->route()->getName(), $fullscreenRoutes)) {
        $flex = 'flex:1 auto;';
    }

    if (! isset($firstTab)) {
        $firstTab = $routeName;
    }
@endphp

@section ('content')
    <div class="flex flex-wrap-reverse" style="{{ $flex }}">

        @yield('contentLeftLeft')
        <div class="flex flex-1 overflow-y-auto">
            <div class="flex flex-1 card card-inverse">
                <ul class="flex p-2 pl-2 space-x-2 list-none dark:bg-primary-dark dark:text-secondary-gray">
                    @yield('content_top')
                </ul>
                @if(isset($tabs))
                <div class="px-2 dark:bg-black-dark bg-slate-300 border-b border-gray-300 dark:border-black-dark d-print-none shadow-md" style="padding-top:0;display:flex;">
                    <ul id="tabs" class="flex pl-3 space-x-2 nav card-header-tabs nms-tabs text-black dark:text-primary-gray" style="width:100%;">
                        @foreach ($tabs as $tab)

                            {{-- Logging tab --}}
                            @if ($tab['name'] == "Logging")
                                <li class="order-12 p-1 ml-auto" role="tab" style="float: right">
                                    <a id="loggingtab" class="p-0" href="#logging" data-toggle="tab">
                                        <span class="{{ $routeName == $tab['route'] ? 'text-cyan-500' : 'text-gray-800'}} "><i class="fa fa-{{ $tab['icon'] ?? 'history' }}"></i> Logging</span>
                                    </a>
                                </li>
                                @continue
                            @endif

                            {{-- Link to separate view --}}
                            @if (isset($tab['route']))
                                <li class="p-1 " role="tab">
                                    <a href="{{ route($tab['route'], is_array($tab['link']) ? $tab['link'] : [$tab['link']]) }}{{ $routeName == $tab['route'] ? '#' : ''}}" class="{{ $routeName == $tab['route'] ? 'active' : ''}} p-0">
                                        <span class="{{ $routeName == $tab['route'] ? 'text-cyan-500' : 'text-gray-800'}}">
                                        {{-- @if (isset($tab['icon']))
                                            <i class="fa fa-{{ $tab['icon'] }}"></i>
                                        @endif --}}
                                        {{ Lang::has('view.tab.'.$tab['name']) ? trans('view.tab.'.$tab['name']) : $tab['name'] }}
                                        </span>
                                    </a>
                                </li>
                                @continue
                            @endif

                            {{-- Other tabs --}}
                            {{-- probably the <a> tag must be set to active according to docu --}}
                            <li class="p-1 " role="tab">
                                <a id="{{$tab['name'].'tab'}}" class="{{ $firstTab == $tab['name'] ? 'active' : '' }} p-0" href="#{{ $tab['name'] }}" data-toggle="tab">
                                    <span class="{{ $firstTab == $tab['name'] ? 'text-cyan-500' : 'text-gray-800' }}">
                                    {{-- @if (isset($tab['icon']))
                                        <i class="fa fa-{{$tab['icon']}}"></i>
                                    @endif --}}
                                    {{ Lang::has('view.tab.'.$tab['name']) ? trans('view.tab.'.$tab['name']) : $tab['name'] }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="flex-wrap d-flex dark:bg-black-dark mt-3.5 dark:mt-0" style="display:flex;flex: 1;">
                    <div class="card card-inverse col-lg-{{(!isset($relations) || empty($relations)) ? '12' : $edit_left_md_size}}" style="{{ (isset($withHistory) || in_array(\Request::route()->getName(), $fullscreenRoutes)) ? 'display:flex;flex: 1;' : '' }}">
                        @yield('content_left')
                    </div>
                    @yield('content_right')
                </div>
            </div>
        </div>
        @yield('contentRightRight')

    </div>

    @yield('contentBottom')
@stop
