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
{{-- begin Navbar --}}
<nav v-pre id="header" class="header navbar navbar-expand navbar-default navbar-fixed-top d-print-none">
    {{-- only one row Navbar --}}
    <div class="flex justify-between dark:text-primary-gray">
        {{-- begin mobile sidebar expand / collapse button --}}
        <button type="button" class="navbar-toggle m-l-20" data-click="sidebar-toggled">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        {{-- NMSPrime Logo with link to global dashboard --}}
        <span class="navbar-brand d-none d-sm-none d-md-block">
            <a href="{{ $nmsprimeLogoLink }}" target="_top" alt="NMS Prime Logo">
            <img src="{{ asset('images/nmsprime-logo.png') }}" class="h-10 ml-2 -mt-2">
            </a>
        </span>

        @if ($headline &&
            Illuminate\Support\Str::endsWith(
                request()->route()->getName(),
                'edit',
            ))
            <ul class="nav nav-pills mt-2 d-flex d-lg-none sm:hidden">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle bg-dark active text-white"
                        style="display:flex; align-items:center;" href="#"
                        onClick="document.getElementById('breadcrumbscroller').style.transform = 'translateY(0)';document.getElementById('breadcrumbexit').style.transform = 'translateY(0)';">
                        <i class="fa fa-ellipsis-h fa-2x" aria-hidden="true"></i>
                        <div class="d-none d-md-block pl-2">{{ trans('view.Header_Dependencies') }}</div>
                    </a>
                </li>
            </ul>
        @endif
        {{-- end mobile sidebar expand / collapse button --}}
        <div id="breadcrumbscroller" class="d-flex col tab-overflow sm:hidden">
            <ul class="nav nav-pills p-t-5">
                <li class="prev-button "><a href="javascript:;" data-click="prev-tab" class="m-t-10"><i
                            class="fa fa-arrow-left"></i></a></li>
                @yield('content_top')
                <li class="next-button"><a href="javascript:;" data-click="next-tab" class="m-t-10"><i
                            class="fa fa-arrow-right"></i></a></li>
            </ul>
        </div>
        <div id="breadcrumbexit" class="d-lg-none sm:hidden">
            <a href="#" class="text-dark"
                onClick="document.getElementById('breadcrumbscroller').style.transform = 'translateY(-150px)';document.getElementById('breadcrumbexit').style.transform = 'translateY(-150px)';">
                <i class="fa fa-close fa-2x"></i>
            </a>
        </div>

        <ul class="flex items-center">
            {{-- @if (Module::collections()->has(['Dashboard', 'HfcBase']) && is_object($modem_statistics) && $modem_statistics->all)
            {{-- Modem Statistics (Online/Offline)
            <li class='d-none d-md-flex' style='font-size: 2em; font-weight: bold'>
              <a class="d-flex" href="{{ route('HfcBase.index') }}" style="text-decoration: none;" data-toggle="tooltip" data-html="true" data-placement="auto" title="{!! $modem_statistics->text !!}">
                  <i class="{{ $modem_statistics->fa }} fa-lg text-{{ $modem_statistics->style }}"></i>
                  <div class="badge badge-{{ $modem_statistics->style }} d-none d-lg-block">{!! $modem_statistics->text !!}</div>
              </a>
            </li>
          @endif --}}

            {{-- count of user interaction needing EnviaOrders --}}
            {{-- @if (Module::collections()->has('ProvVoipEnvia'))
          <li  class='d-none d-md-flex' style='font-size: 2em; font-weight: bold'>
            <a href="{{ route('EnviaOrder.index', ['show_filter' => 'action_needed']) }}" target="_self" style="text-decoration: none;">
              @if ($envia_interactioncount > 0)
                <div class="d-flex" data-toggle="tooltip" data-placement="auto" title="{{ $envia_interactioncount }} {{ trans_choice('messages.envia_interaction', $envia_interactioncount )}}">
                  <i class="fa fa-times fa-lg text-danger"></i>
                  <div class="badge badge-danger d-none d-lg-block" style="width:110px;word-wrap:break-word;white-space:normal;">{{ $envia_interactioncount }} {{ substr(trans_choice('messages.envia_interaction', $envia_interactioncount), 0, 19) }}</div>
                </div>
              @else
                <div data-toggle="tooltip" data-placement="auto" title="{{ trans('messages.envia_no_interaction')}}">
                  <i class="fa fa-check fa-lg text-success"></i>
                </div>
              @endif
            </a>
          </li>
        @endif --}}

            {{-- quickview pre-selected network  --}}
            @if (Module::collections()->has('CoreMon') && isset($quick_view_network))
            <li class="nav-item dropdown d-none d-md-flex">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false" style="padding: 12px 10x 8px 8px;">
                    <img src="{{asset('storage/public/overview_network.webp')}}" class="w-8"/>
                </a>
                <div class="dropdown-menu QuickviewNetworkChartContainer" aria-labelledby="navbarDropdown" style="right: 0;left:auto; min-width: 270px;">
                    <navbar-quickview-network title="{{ $quick_view_network['title'] }}" v-bind:active_alarms="{{ $quick_view_network['sum'] }}" v-bind:info="{{ $quick_view_network['info'] }}" v-bind:warning="{{ $quick_view_network['warning'] }}" v-bind:critical="{{ $quick_view_network['critical'] }}"/>
                </div>
            </li>
            @endif

            {{-- Help Section --}}
            <li class="nav-item dropdown d-none d-md-flex">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false" style="padding: 12px 10x 8px 8px;">
                    <i class="fa fa-question fa-2x" aria-hidden="true"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="right: 0;left:auto;">
                    <a class="dropdown-item" href="https://devel.roetzer-engineering.com/" target="_blank">
                        <i class="fa fa-question-circle" aria-hidden="true" style="width: 20px;"></i>Documentation
                    </a>
                    <a class="dropdown-item" href="https://www.youtube.com/channel/UCpFaWPpJLQQQLpTVeZnq_qA"
                        target="_blank">
                        <i class="fa fa-tv" aria-hidden="true" style="width: 20px;"></i>Youtube
                    </a>
                    <a class="dropdown-item" href="https://nmsprime.com/forum" target="_blank">
                        <i class="fa fa-wpforms" aria-hidden="true" style="width: 20px;"></i>Forum
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href={{ route('SupportRequest.index') }}>
                        <i class="fa fa-envelope-open" aria-hidden="true" style="width: 20px;"></i>Professional Help
                    </a>
                </div>
            </li>

            {{-- global search form --}}
            <li class="nav-item d-flex">
                <a id="togglesearch" href="javascript:;" class="waves-effect waves-light" data-toggle="navbar-search">
                    <i class="fa fa-search fa-2x" aria-hidden="true"></i>
                </a>
            </li>

            {{-- Notification Section --}}
            @include('bootstrap._navbar-notifications')

            {{-- User Menu --}}
            <li class="nav-item dropdown d-flex m-r-10">
                <a id="navbarDropdown" class="nav-link d-flex align-items-end dropdown-toggle" href="#" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-user-circle-o fa-2x d-inline" aria-hidden="true"></i>
                    <span class="d-none d-md-inline">
                        {{ $user->first_name . ' ' . $user->last_name }}
                    </span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="right: 0;left:auto;">
                    <a class="dropdown-item" href="{{ route('User.profile', $user->id) }}">
                        <i class="fa fa-cog" aria-hidden="true"></i>
                        {{ \App\Http\Controllers\BaseViewController::translate_view('UserSettings', 'Menu') }}
                    </a>
                    @if (Bouncer::can('update', App\User::class))
                        <a class="dropdown-item" href="{{ route('User.index') }}">
                            <i class="fa fa-cogs" aria-hidden="true"></i>
                            {{ \App\Http\Controllers\BaseViewController::translate_view('UserGlobSettings', 'Menu') }}
                        </a>
                    @endif
                    @if (Bouncer::can('update', App\Role::class))
                        <a class="dropdown-item" href="{{ route('Role.index') }}">
                            <i class="fa fa-users" aria-hidden="true"></i>
                            {{ \App\Http\Controllers\BaseViewController::translate_view('UserRoleSettings', 'Menu') }}
                        </a>
                    @endif
                    <div class="dropdown-divider"></div>
                    {!! Form::open(['url' => route('logout.post')]) !!}
                    <button class="dropdown-item">
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                        {{ \App\Http\Controllers\BaseViewController::translate_view('Logout', 'Menu') }}
                    </button>
                    {!! Form::close() !!}
                </div>
            </li>
        </ul>
        {{-- end header navigation right --}}
        <div class="search-form bg-white" style="height: auto;">
            <form id="globalSearchForm" class="form-open" method="GET" onsubmit="linkTag();">
                <div class="btn-group search-btn">
                    <div class="input-group-append">
                        <button class="btn btn-primary" onsubmit="linkTag();"
                            for="prefillSearchbar">{{ trans('view.jQuery_sSearch') }}</button>
                    </div>
                    <select class="custom-select" id="prefillSearchbar" onchange="getSearchTag();">
                        <option selected value="" data-route="{{ route('Base.globalSearch') }}">
                            {{ trans('view.jQuery_All') }}</option>
                        @if (Module::collections()->has('ProvMon'))
                            {
                            <option value="ip:" data-route="{{ route('Ip.globalSearch') }}">IP</option>
                        @endif
                    </select>
                </div>
                <input id="globalSearch" type="text" name="query" class="form-control navbar"
                    placeholder="{{ \App\Http\Controllers\BaseViewController::translate_view('EnterKeyword', 'Search') }}">
                <a href="#" class="close" data-dismiss="navbar-search"><i class="fa fa-angle-up fa-2x"
                        aria-hidden="true"></i></a>
            </form>
        </div>
    </div> {{-- End ROW --}}
</nav>
