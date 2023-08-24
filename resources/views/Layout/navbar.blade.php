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
<nav v-pre id="header" class="fixed h-[60px] header navbar navbar-expand navbar-default navbar-fixed-top d-print-none dark:shadow-slate-100 dark:shadow">
    {{-- only one row Navbar --}}
    <div class="flex justify-between h-full dark:text-slate-100">
        {{-- begin mobile sidebar expand / collapse button --}}
        <button type="button" class="navbar-toggle m-l-20" v-on:click="toggleMobileSidebar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        {{-- NMSPrime Logo with link to global dashboard --}}
        <span class="hidden navbar-brand d-sm-none d-md-block">
            <a href="{{ $nmsprimeLogoLink }}" target="_top" alt="NMS Prime Logo">
            <img @if($user->theme_color === 'dark_theme_config.css') src="{{ asset('images/nmsprime-logo-white.png') }}" @else src="{{ asset('images/nmsprime-logo.png') }}" @endif  class="h-10 ml-2 -mt-2 dark:bg-slate-900">
            </a>
        </span>

        @if ($headline && Illuminate\Support\Str::endsWith(request()->route()->getName(), 'edit'))
            <div class="flex my-2 nav nav-pills md:hidden">
                <a href="#"
                    class="flex items-center px-3 text-white no-underline rounded bg-dark"
                    v-on:click="breadcrumbScroller = true">
                    <i class="m-0 fa fa-ellipsis-h fa-2x" aria-hidden="true"></i>
                    <div class="hidden pl-2 sm:block">{{ trans('view.Header_Dependencies') }}</div>
                </a>
            </div>
            <div v-cloak
                class="absolute bg-white flex z-10 w-full h-[60px] items-center transition-transform duration-300"
                :class="{'-translate-y-full': !breadcrumbScroller, 'translate-y-0': breadcrumbScroller}"
            >
                {{-- end mobile sidebar expand / collapse button --}}
                <div class="flex items-center flex-1 h-full px-3 space-x-2 overflow-x-scroll whitespace-nowrap md:hidden">
                    @yield('content_top')
                </div>
                <div>
                    <a href="#" class="mx-4 text-dark" v-on:click="breadcrumbScroller = false">
                        <i class="fa fa-close fa-2x"></i>
                    </a>
                </div>
            </div>
        @endif
        <ul class="flex items-center">
            {{-- @if (Module::collections()->has(['Dashboard', 'HfcBase']) && is_object($modem_statistics) && $modem_statistics->all)
            {{-- Modem Statistics (Online/Offline)
            <li class='hidden d-mflex' style='font-size: 2em; font-weight: bold'>
              <a class="flex" href="{{ route('HfcBase.index') }}" style="text-decoration: none;" data-toggle="tooltip" data-html="true" data-placement="auto" title="{!! $modem_statistics->text !!}">
                  <i class="{{ $modem_statistics->fa }} fa-lg text-{{ $modem_statistics->style }}"></i>
                  <div class="badge badge-{{ $modem_statistics->style }} hidden d-lg-block">{!! $modem_statistics->text !!}</div>
              </a>
            </li>
          @endif --}}

            {{-- count of user interaction needing EnviaOrders --}}
            @if (Module::collections()->has('ProvVoipEnvia'))
                <li  class='hidden d-mflex' style='font-size: 2em; font-weight: bold'>
                    <a href="{{ route('EnviaOrder.index', ['show_filter' => 'action_needed']) }}" target="_self" style="text-decoration: none;">
                    @if ($envia_interactioncount > 0)
                        <div class="flex" data-toggle="tooltip" data-placement="auto" title="{{ $envia_interactioncount }} {{ trans_choice('messages.envia_interaction', $envia_interactioncount )}}">
                        <i class="fa fa-times fa-lg text-danger"></i>
                        <div class="hidden badge badge-danger d-lg-block" style="width:110px;word-wrap:break-word;white-space:normal;">{{ $envia_interactioncount }} {{ substr(trans_choice('messages.envia_interaction', $envia_interactioncount), 0, 19) }}</div>
                        </div>
                    @else
                        <div data-toggle="tooltip" data-placement="auto" title="{{ trans('messages.envia_no_interaction')}}">
                        <i class="fa fa-check fa-lg text-success"></i>
                        </div>
                    @endif
                    </a>
                </li>
            @endif

            {{-- quickview pre-selected network  --}}
            @if (Module::collections()->has('CoreMon') && isset($quick_view_network))
            <li class="hidden nav-item dropdown d-mflex">
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
            <li class="hidden nav-item dropdown d-mflex">
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
            <li class="flex nav-item">
                <a href="javascript:;" class="waves-effect waves-light" v-on:click="showSearchbar = true">
                    <i class="fa fa-search fa-2x" aria-hidden="true"></i>
                </a>
            </li>

            {{-- Notification Section --}}
            @include('bootstrap._navbar-notifications')

            {{-- User Menu --}}
            <li class="flex nav-item dropdown m-r-10">
                <a id="navbarDropdown" class="flex nav-link align-items-end dropdown-toggle" href="#" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-user-circle-o fa-2x d-inline" aria-hidden="true"></i>
                    <span class="hidden d-md-inline">
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
        <div v-cloak class="bg-white absolute w-full h-[60px] transition-transform duration-300"
            :class="{'-translate-y-full': !showSearchbar, 'translate-y-0': showSearchbar}">
            <form class="flex items-center h-full px-2 form-open" method="GET" :action="selectedRoute">
                @if (Module::collections()->has('ProvMon'))
                <div class="w-16 mx-2">
                    <select2 v-model="selectedRoute" class="text-normal">
                        <option selected value="{{ route('Base.globalSearch') }}">
                            {{ trans('view.jQuery_All') }}
                        </option>
                        <option value="{{ route('Ip.globalSearch') }}">IP</option>
                    </select2>
                </div>
                @endif
                <input ref="searchfield" type="text" name="query" class="w-2/3 text-lg outline-none md:flex-1 md:text-2xl md:px-6" v-on:keydown.esc="blurInput" v-model="search"
                    placeholder="{{ \App\Http\Controllers\BaseViewController::translate_view('EnterKeyword', 'Search') }}">
                <button class="btn btn-primary md:flex" for="prefillSearchbar">
                    <i class="fa fa-search"></i>
                    <span class="hidden m-0 md:block md:mr-1">{{ trans('view.jQuery_sSearch') }}</span>
                </button>
                <div v-on:click="showSearchbar = false" class="mx-4 cursor-pointer">
                    <i class="fa fa-angle-up fa-2x" :aria-hidden="showSearchbar"></i>
                </div>
            </form>
        </div>
    </div> {{-- End ROW --}}
</nav>
