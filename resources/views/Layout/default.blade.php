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
<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ $html_title }}</title>
    @include('bootstrap.header')
    @yield('head')
    @livewireStyles()
</head>

<body {{ isset($body_onload) ? "onload=$body_onload()" : '' }} @if($user->theme_color === 'dark_theme_config.css') class="dark" @endif data-theme_color="{{$user->theme_color}}">
    @include('Layout.navbar')
    @include('Layout.sidebar')
    @yield('sidebar-right')

    <div id="page-container" class="flex flex-column page-sidebar-fixed page-header-fixed in" style="min-height:100%;">
        <Transition>
            <div id="content"
                class="mt-[60px] md:mt-0 flex flex-column flex-1 transition-all duration-200 ease-in-out {{ cache('sidebar.pinnedState.'.$user->login_name) ? (Module::collections()->has('CoreMon') ? 'md:ml-[22.75rem]' : 'md:ml-[17.5rem]') : (Module::collections()->has('CoreMon') ? 'md:ml-[6.25rem]' : 'md:ml-[1.5rem]') }}"
                :class="{
                    '{{ Module::collections()->has('CoreMon') ? 'md:!ml-[22.75rem]' : 'md:!ml-[17.5rem]' }}': !store.minified,
                    '{{ Module::collections()->has('CoreMon') ? 'md:!ml-[6.25rem]' : 'md:!ml-[1.5rem]' }}': store.minified,
                    'mr-0' : !store.hasSidebarRight,
                    'mr-4': store.hasSidebarRight &&  store.minifiedRight,
                    'mr-[17.5rem]': store.hasSidebarRight && !store.minifiedRight
                }">
                <vue-snotify></vue-snotify>

                @if (session('GlobalNotification'))
                    <div style="padding-top:1rem;">
                        @foreach (session('GlobalNotification') as $name => $options)
                            <div class="alert alert-{{ $options['level'] }} alert-dismissible fade show" role="alert">
                                <h4 class="text-center alert-heading">{{ trans('messages.' . $options['message']) }} </h4>
                                <p class="mb-0 text-center">
                                    {{ trans('messages.' . $options['reason']) }}
                                    <a href="{{ route('User.profile', $user->id) }}" class="alert-link">
                                        {{ trans('messages.PasswordClick') }}
                                    </a>
                                </p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if (session('DashboardNotification'))
                    @foreach (session('DashboardNotification') as $name => $options)
                        <div class="alert alert-{{ $options['level'] }} alert-dismissible fade show" role="alert">
                            <p class="mb-0 text-center">
                                {{ $options['message'] }}
                            </p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endforeach
                @endif
                <div class="flex flex-col flex-1 mt-2 d-print-flex">
                    @yield ('content')
                </div>
            </div>
        </Transition>
        <overlay/>
    </div>

    @livewireScripts()
    @include('bootstrap.footer')
    @yield ('form-javascript')
    @yield ('javascript')
    @yield ('javascript_extra')
    @yield ('mycharts')
    @include('Generic.userGeopos')

    {{-- scroll to top btn --}}
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade flex"
        data-click="scroll-top" style="justify-content: space-around;align-items: center">
        <i class="m-0 fa fa-angle-up"></i>
    </a>

</body>

</html>
