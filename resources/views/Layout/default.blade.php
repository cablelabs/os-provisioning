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
</head>

<body {{ isset($body_onload) ? "onload=$body_onload()" : '' }} data-theme="{{$user->theme_color}}">
    @include('Layout.navbar')
    @include('Layout.sidebar')

    @if (Illuminate\Support\Str::contains(request()->route()->getName(), ['index', 'association', 'alarms']))
        @include('Layout.sidebar-right')
    @endif
    <div id="page-container" class="d-flex flex-column fade page-sidebar-fixed page-header-fixed in"
        :class="{ 'page-sidebar-minified': store.minified }" style="min-height:100%;">

        <div id="content"
            class="d-flex flex-column flex-1 transition-all duration-200 ml-[5.25rem]"
            :class="{'ml-[21.25rem]': !store.minified, 'mr-4': store.minifiedRight, 'mr-[17.5rem]': !store.minifiedRight}">
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
            <div class="flex flex-col flex-1 d-print-flex">
                @yield ('content')
            </div>
        </div>
        <overlay/>
    </div>


    @include('bootstrap.footer')
    @yield ('form-javascript')
    @yield ('javascript')
    @yield ('javascript_extra')
    @yield ('mycharts')
    @include('Generic.userGeopos')

    {{-- scroll to top btn --}}
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade d-flex"
        data-click="scroll-top" style="justify-content: space-around;align-items: center">
        <i class="m-0 fa fa-angle-up"></i>
    </a>

</body>

</html>
