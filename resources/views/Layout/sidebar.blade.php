<sidebar id="sidebar" data-net-count='{{ $netCount ?? 0 }}' data-netelements='@json($networks ?? new stdClass())'
    data-favorites='@json($favorites ?? new stdClass())' class="fixed top-0 left-0 z-0 flex flex-col h-full">
    <div class="mt-16 md:mt-[3.25rem] flex flex-1 text-gray-200">
        @if (Module::collections()->has('CoreMon'))
        <div class="z-20 flex flex-col justify-between w-16 bg-black-dark">
            <div>
                <div
                    class="flex flex-col items-center justify-center space-y-2 text-xs text-center border-b border-gray-200">
                    <div class="flex flex-col items-center w-full p-2 transition duration-150 ease-out hover:bg-zinc-900 hover:text-lime-nmsprime hover:cursor-pointer hover:ease-in"
                        :class="{ 'bg-zinc-900': menu == 'Core Network' }" v-on:click="openSidebar('Core Network');">
                        <svg version="1.1" viewBox="0 0 96 96" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
                            <path
                                d=" M 53.5 79.5 C 60 71.5 67.2 61.4 67.9 50 L 79.9 50 C 79 64.9 67.9 77 53.5 79.5 Z M 16.1 50 L 28.1 50 C 28.9 61.4 36 71.5 42.5 79.5 C 28.1 77 17 64.9 16.1 50 Z M 42.5 16.5 C 36 24.5 28.8 34.6 28.1 46 L 16.1 46 C 17 31.1 28.1 19 42.5 16.5 Z M 50 50 L 63.9 50 C 63.1 60.3 56.4 69.6 50 77.5 L 50 50 Z M 46 50 L 46 77.5 C 39.6 69.6 32.9 60.3 32.1 50 L 46 50 Z M 50 18.5 C 56.4 26.4 63.1 35.6 63.9 46 L 50 46 L 50 18.5 Z M 46 46 L 32.1 46 C 32.9 35.7 39.6 26.4 46 18.5 L 46 46 Z M 79.9 46 L 67.9 46 C 67.2 34.6 60 24.5 53.5 16.5 C 67.9 19 79 31.1 79.9 46 Z M 48 10 C 27 10 10 27 10 48 C 10 69 27 86 48 86 C 69 86 86 69 86 48 C 86 27 69 10 48 10 Z"
                                stroke="none" stroke-width="1" stroke-dashoffset="1" fill="currentColor"
                                fill-opacity="1" />
                        </svg>
                        <div class="pt-2">Core Network</div>
                    </div>
                    <div class="flex flex-col items-center w-full p-1 transition duration-150 ease-out hover:bg-zinc-900 hover:text-lime-nmsprime hover:cursor-pointer hover:ease-in"
                        :class="{ 'bg-zinc-900': menu == 'Access Network' }"
                        v-on:click="openSidebar('Access Network');">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 367 341" class="w-6 h-6"
                            fill="CurrentColor">
                            <path
                                d="M80.482 339.661c-1.056-.271-3.504-.53-5.44-.575-2.264-.053-4.741-.65-6.944-1.676l-3.423-1.594-26.977-.006-26.976-.006-3.2-1.513c-4.289-2.027-6.479-4.53-7.066-8.074-.679-4.093-.71-77.07-.034-81.127.458-2.757 1.02-3.782 3.1-5.662 4.305-3.888 5.325-4.09 20.62-4.09h13.78v-68.059c0-77.315-.31-72.518 5.43-83.942 6.587-13.106 18.956-22.996 33.232-26.572 4.556-1.141 7.261-1.268 27.04-1.268h21.978V10.948l1.603-3.217c2.19-4.397 4.153-6.055 8.136-6.877 3.99-.824 78.911-.926 84.165-.114 3.914.604 6.897 3.092 9.056 7.552 1.306 2.698 1.36 3.683 1.36 25.006v22.198h25.498c29.245 0 31.045.236 41.382 5.431 9.864 4.957 17.452 12.546 22.409 22.41 5.213 10.373 5.43 12.058 5.43 42.022v26.137l15.2.006c14.645.006 15.318.062 18.4 1.52 4.29 2.027 6.48 4.53 7.067 8.074.663 3.996.69 77.736.03 81.28-.635 3.417-3.38 6.66-7.096 8.384-2.269 1.053-4.327 1.19-18.08 1.202l-15.52.014v22.937c0 26.378-.318 28.648-5.431 38.823-6.655 13.242-20.035 24.01-31.661 25.481-5.83.737-204.272 1.163-207.068.444zm199.68-12.744c1.76-.448 5.626-1.973 8.59-3.39 4.237-2.023 6.449-3.632 10.329-7.512 3.88-3.88 5.489-6.092 7.512-10.328 1.416-2.965 2.913-6.83 3.325-8.59.448-1.91.888-11.65 1.092-24.16l.342-20.96-15.915-.007c-15.32-.006-16.035-.062-19.12-1.498-1.762-.821-4.139-2.557-5.281-3.858-4.13-4.703-3.994-3.2-3.99-44.476.001-23.196.249-38.407.647-39.84.86-3.093 5.11-7.818 8.518-9.468 2.57-1.243 3.846-1.333 18.941-1.333h16.187l-.326-24.16c-.205-15.283-.604-25.336-1.085-27.36-2.615-11.012-11.12-21.629-21.175-26.432-2.965-1.416-6.831-2.916-8.591-3.333-2.004-.475-11.989-.88-26.72-1.086l-23.52-.329v10.824c0 10.35-.072 10.97-1.65 14.135-1.86 3.733-4.454 6.185-8.394 7.936-2.54 1.129-4.828 1.184-42.56 1.019-44.612-.195-42.16.058-47.088-4.87-4.08-4.079-4.598-6.13-4.614-18.262l-.014-10.797-20 .347c-11.67.203-21.333.659-23.2 1.094C71.449 72.78 60.777 81.324 55.97 91.386c-1.417 2.964-2.938 6.83-3.382 8.59-.615 2.44-.864 18.912-1.047 69.28l-.241 66.08 17.47.014c16.19.013 17.682.11 20.35 1.318 3.73 1.688 6.148 4.018 8.06 7.767l1.54 3.019v38.122c0 32.021-.15 38.48-.936 40.364l-.937 2.242 90.057-.225c70.219-.175 90.762-.404 93.257-1.04z" />
                        </svg>
                        <div class="pt-2">Access Network</div>
                    </div>
                    <a href="{{ route('GuiLog.index') }}"
                        class="flex flex-col items-center w-full p-2 transition duration-150 ease-out hover:bg-zinc-900 hover:text-lime-nmsprime hover:cursor-pointer hover:ease-in">
                        <svg version="1.1" viewBox="0 0 96 96" class="w-12 h-12" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
                            <rect x="20" y="43.79" rx="2" ry="2" width="10" height="10" stroke="none" stroke-width="1"
                                fill="currentColor" fill-opacity="1" />
                            <rect x="34" y="40.79" rx="2" ry="2" width="16" height="16" stroke="none" stroke-width="1"
                                fill="currentColor" fill-opacity="1" />
                            <rect x="54" y="37.79" rx="2" ry="2" width="22" height="22" stroke="none" stroke-width="1"
                                fill="currentColor" fill-opacity="1" />
                        </svg>
                        <div>Activity</div>
                    </a>
                    <a href="{{ route('Apps.active') }}"
                        class="flex flex-col items-center w-full p-2 transition duration-150 ease-out hover:bg-zinc-900 hover:text-lime-nmsprime hover:cursor-pointer hover:ease-in">
                        <svg version="1.1" viewBox="0 0 96 96" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
                            <rect x="0" y="0" rx="8" ry="8" width="46" height="46" stroke="none" stroke-width="1"
                                fill="currentColor" fill-opacity="1" />
                            <rect x="0" y="50" rx="8" ry="8" width="46" height="46" stroke="none" stroke-width="1"
                                fill="currentColor" fill-opacity="1" />
                            <rect x="50" y="0" rx="8" ry="8" width="46" height="46" stroke="none" stroke-width="1"
                                fill="currentColor" fill-opacity="1" />
                            <rect x="50" y="50" rx="8" ry="8" width="46" height="46" stroke="none" stroke-width="1"
                                class="text-lime-nmsprime" fill="currentColor" fill-opacity="1" />
                        </svg>
                        <div class="pt-2">Apps</div>
                    </a>
                    <div
                        class="mb-2 transition duration-150 ease-out hover:cursor-pointer hover:text-lime-nmsprime hover:ease-in">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                </div>
                <div
                    class="flex flex-col items-center justify-center my-2 space-y-2 transition duration-150 ease-out hover:text-lime-nmsprime hover:cursor-pointer hover:ease-in">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>Help</div>
                </div>
            </div>
            <a href="https://devel.nmsprime.com/confluence/display/nmsprimeforum/The+Official+NMS+Prime+Forum"
                class="flex flex-col items-center mb-6 space-y-2 text-xs transition duration-150 ease-out hover:text-lime-nmsprime hover:cursor-pointer hover:ease-in">
                <svg version="1.1" viewBox="0 0 96 96" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <circle cx="69.25" cy="56.53" r="7.78" stroke="none" stroke-width="1" fill="currentColor"
                        fill-opacity="1" />
                    <circle cx="27.76" cy="56.53" r="7.78" stroke="none" stroke-width="1" fill="currentColor"
                        fill-opacity="1" />
                    <path
                        d=" M 83.26 71.05 C 81.0154 69.3044 78.4191 68.066 75.65 67.42 C 73.5687 66.812 71.4175 66.4758 69.25 66.42 C 67.0807 66.4151 64.9241 66.7526 62.86 67.42 C 60.7943 67.97 58.8201 68.819 57 69.94 L 56.64 70.35 C 59.5474 71.1348 62.2677 72.495 64.64 74.35 C 66.5046 75.7199 67.5923 77.9065 67.56 80.22 L 67.56 82 L 84.81 82 L 84.81 74.17 C 84.8441 72.937 84.2632 71.7677 83.26 71.05 Z"
                        stroke="none" stroke-width="1" fill="currentColor" fill-opacity="1" />
                    <path
                        d=" M 29.48 80.22 C 29.4816 77.9674 30.5176 75.8402 32.29 74.45 L 32.4 74.35 L 32.53 74.26 C 34.9304 72.5518 37.5868 71.2354 40.4 70.36 C 40.2 70.15 40.01 69.93 39.83 69.71 C 38.0558 68.6793 36.143 67.9081 34.15 67.42 C 32.072 66.8128 29.9242 66.4767 27.76 66.42 C 25.5873 66.4144 23.4274 66.7519 21.36 67.42 C 18.6297 68.1732 16.0536 69.402 13.75 71.05 C 12.78 71.7965 12.2053 72.9461 12.19 74.17 L 12.19 82 L 29.48 82 Z"
                        stroke="none" stroke-width="1" fill="currentColor" fill-opacity="1" />
                    <path
                        d=" M 32.94 88 L 32.94 80.22 C 32.9447 78.9963 33.522 77.8454 34.5 77.11 C 36.799 75.4543 39.3765 74.2248 42.11 73.48 C 44.1731 72.8082 46.3303 72.4706 48.5 72.48 C 50.668 72.5309 52.8198 72.8672 54.9 73.48 C 57.6721 74.1175 60.2701 75.3567 62.51 77.11 C 63.5168 77.8211 64.1021 78.9879 64.07 80.22 L 64.07 88 Z"
                        stroke="none" stroke-width="1" fill="currentColor" fill-opacity="1" />
                    <circle cx="48.5" cy="62.58" r="7.78" stroke="none" stroke-width="1" fill="currentColor"
                        fill-opacity="1" />
                    <path
                        d=" M 81 8 L 16.25 8 C 14.0409 8 12.25 9.79086 12.25 12 L 12.25 34 C 12.25 36.2091 14.0409 38 16.25 38 L 31 38 L 31 44 L 37.3 38 L 44.1 38 L 48 44 L 51.6 38 L 58.7 38 L 65 44 L 65 38 L 81 38 C 83.2091 38 85 36.2091 85 34 L 85 12 C 85 9.79086 83.2091 8 81 8 Z M 22.25 17 L 69 17 L 69 19 L 22.25 19 Z M 59 29 L 22.25 29 L 22.25 27 L 59 27 Z M 75 24 L 22.25 24 L 22.25 22 L 75 22 Z"
                        stroke="none" stroke-width="1" fill="currentColor" fill-opacity="1" />
                </svg>
                <div>Community</div>
            </a>
        </div>
        @endif
        <div class="relative z-10 transition-all duration-200"
            :class="{ '-translate-x-full': store.minified }">
            @if (Module::collections()->has('CoreMon'))
            <div v-cloak v-show="menu == 'Core Network'" class="flex w-64 h-full bg-zinc-900 core-network-sidebar">
                <div class="w-full px-3 py-2 text-gray-400">
                    <div class="mb-4 text-base font-semibold text-gray-100">Filter</div>
                    <div class="flex flex-col space-y-4 text-sm">
                        @foreach ([
                            'Network' => [
                                'route' => 'CoreMon.net.overview',
                                'netTypeId' => 1,
                                'var' => 'network',
                            ],
                            'Market' => [
                                'route' => 'CoreMon.market.overview',
                                'netTypeId' => 16,
                                'var' => 'market',
                            ],
                            'Hubsite' => [
                                'route' => 'CoreMon.hubsite.overview',
                                'netTypeId' => 17,
                                'var' => 'hubsite',
                            ],
                            'CCAP Core' => [
                                'route' => 'CoreMon.ccap.overview',
                                'netTypeId' => 18,
                                'var' => 'ccap',
                            ],
                            'DPA' => [
                                'route' => 'CoreMon.dpa.overview',
                                'netTypeId' => 19,
                                'var' => 'dpa',
                            ],
                            'NCS' => [
                                'route' => 'CoreMon.ncs.overview',
                                'netTypeId' => 20,
                                'var' => 'ncs',
                            ],
                            'RPA' => [
                                'route' => 'CoreMon.rpa.overview',
                                'netTypeId' => 21,
                                'var' => 'rpa',
                            ],
                            'RPD' => [
                                'route' => 'CoreMon.rpd.overview',
                                'netTypeId' => 22,
                                'var' => 'rpd',
                            ],
                            'CPE' => [
                                'route' => 'CoreMon.cpe.overview',
                                'netTypeId' => 23,
                                'var' => 'cpe',
                            ],
                        ] as $netType => $options)
                            @include('Layout.sidebar._filter-select', [
                                'name' => $netType,
                                'selected' => $loop->first,
                                'options' => $options,
                            ])
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            <!-- begin "old" sidebar -->
            <div v-cloak v-show="(menu == 'Access Network' || {{ (int)!Module::collections()->has('CoreMon') }}) && !store.minified" class="flex w-64 overflow-y-auto sidebar d-print-none"
                style="position: absolute;padding-top:0;">
                <!-- begin sidebar scrollbar -->
                <ul class="overflow-y-auto" data-scrollbar="true" data-height="100%">
                    <!-- begin sidebar user -->
                    <ul class="nav">
                        <li class="nav-profile">
                            <div class="info">
                                {{ $globalConfig->headline2 }}
                                <small>Version
                                    @if (is_string($version))
                                        {{ $version }}
                                    @else
                                        <b>GIT</b>: <a target="_blank" class="text-success"
                                            href="https://github.com/{{ $version['repo'] }}/tree/{{ $version['branch'] }}">{{ $version['branch'] }}</a>
                                        -
                                        <a target="_blank" class="text-success"
                                            href="https://github.com/{{ $version['repo'] }}/commit/{{ $version['commitLong'] }}">{{ $version['commitShort'] }}</a>
                                    @endif
                                </small>
                            </div>
                        </li>
                    </ul>
                    <!-- end sidebar user -->
                    <!-- begin sidebar nav -->
                    <ul class="nav">
                        <li class="nav-header" style="border-top: 1px solid; font-size: 13px !important;">
                            <a href="{{ route('Apps.active') }}" class="text-success d-inline w-100"
                                style="display: inline-block !important; width: 100%;">{{ trans('messages.nativeApps') }}
                                <i class="fa fa-plus"></i>
                            </a>
                        </li>
                        <!-- Main Menu -->
                        @foreach ($view_header_links as $module_name => $typearray)
                            @php
                                $moduleNameSlug = Str::slug($module_name, '_');
                            @endphp
                            <li id="{{ $moduleNameSlug }}" class="has-sub"
                                :class="{
                                    'active': (lastActive == '{{ $moduleNameSlug }}'),
                                    'position-relative': store.minified
                                }"
                                style="z-index:10000;">
                                <div class="flex recolor sidebar-element">
                                    <a class="flex caret-link"
                                        v-on:click="{{ isset($typearray['link']) ? "!store.minified ? setMenu('{$moduleNameSlug}', false) : ''" : "setMenu('{$moduleNameSlug}')" }}"
                                        href="{{ isset($typearray['link']) ? route($typearray['link']) : 'javascript:;' }}">
                                        @if (is_file(public_path('images/apps/') . $typearray['icon']))
                                            <img src="{{ asset('images/apps/' . $typearray['icon']) }}"
                                                class="mr-2"
                                                style="height: 20px; filter: saturate(25%) brightness(80%);">
                                        @else
                                            <i class="fa fa-fw {{ $typearray['icon'] }} mr-2"></i>
                                        @endif
                                        <span>{{ $typearray['translated_name'] ?? $module_name }}</span>
                                    </a>
                                    @if (isset($typearray['submenu']))
                                        <a class="flex-1 caret-link" href="javascript:;"
                                            v-on:click.stop="setMenu('{{ $moduleNameSlug }}')"
                                            style="height: 20px; display:block; text-align: right">
                                            <i class="fa fa-caret-right"
                                                :class="{
                                                    'fa-rotate-90': activeItem == '{{ $moduleNameSlug }}' &&
                                                        ! isCollapsed
                                                }"
                                                style="transition:all .25s;"></i>
                                        </a>
                                    @endif
                                </div>
                                <!-- SubMenu -->
                                @isset($typearray['submenu'])
                                    <transition name="accordion" v-on:before-enter="beforeEnter" v-on:enter="enter"
                                        v-on:before-leave="beforeLeave" v-on:leave="leave" v-on:after-leave="afterLeave">
                                        <ul v-show="activeItem == '{{ $moduleNameSlug }}' && ! isCollapsed"
                                            class="pl-0 m-0 sidebar-hover p-b-10"
                                            :class="{
                                                'minifiedMenu': (showMinifiedHoverMenu && activeItem ==
                                                    '{{ $moduleNameSlug }}' && !isCollapsed)
                                            }"
                                            style="transition:all .3s linear;overflow:hidden;list-style-type: none;background: #1a2229;display:none;">
                                            @foreach ($typearray['submenu'] as $type => $valuearray)
                                                <li id="menu-{{ Str::slug($type, '_') }}"
                                                    class="p-l-20 {{ $loop->first ? 'p-t-10' : '' }}"
                                                    :class="{
                                                        active: (lastClicked ==
                                                            'menu-{{ Str::slug($type, '_') }}')
                                                    }"
                                                    v-on:click="setSubMenu('menu-{{ Str::slug($type, '_') }}')">
                                                    <a href="{{ route($valuearray['link']) }}"
                                                        style="display:block;padding:5px 20px;color:#889097;overflow: hidden;white-space:nowrap;font-weight:300;text-decoration:none;">
                                                        <i class="fa fa-fw {{ $valuearray['icon'] }}"></i>
                                                        <span>{{ $type }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </transition>
                                @endisset
                                <!-- End SubMenu-->
                            </li>
                        @endforeach
                        <!-- End Menu -->

                        <li class="mt-4 nav-header" style="border-top: 1px solid; font-size: 13px !important;">
                            <a href="{{ route('Apps.active') }}" class="text-success d-inline w-100"
                                style="display: inline-block !important; width: 100%;">{{ trans('messages.externalApps') }}
                                <i class="fa fa-plus"></i>
                            </a>
                        </li>
                        @foreach ($externalApps as $appName => $externalApp)
                            @if ($externalApp['state'] == 'active' && file_exists(public_path('images/' . $externalApp['icon'])))
                                <li>
                                    <div class="sidebar-element recolor">
                                        <a href="{{ $externalApp['link'] }}" class="flex">
                                            <img src="{{ asset('images/' . $externalApp['icon']) }}"
                                                class="mr-4 external-app-mini">
                                            <span>{{ $appName }}</span>
                                        </a>
                                    </div>
                                </li>
                            @endif
                        @endforeach

                        @if (Module::collections()->has('HfcBase') &&
                            auth()->user()->can('view', Modules\HfcBase\Entities\TreeErd::class))
                            <li v-cloak v-show="!store.minified" class="mt-4 nav-header align-items-center no-content-pseudo"
                                style="border-top:1px solid;font-size:13px;width: 100%;display:flex;justify-content:space-between;">
                                <div class="text-success" style="flex:1;">{{ trans('view.Menu_Nets') }}</div>
                                <div v-on:click.stop="setVisibility" style="height:1.35rem;">
                                    <i class="m-0 text-light fa" :class="isVisible ? 'fa-eye' : 'fa-eye-slash'"
                                        style="cursor: pointer;"></i>
                                </div>
                                <transition enter-class="toggleWidthStart" enter-to-class="toggleWidthEnd"
                                    leave-class="toggleWidthEnd" leave-to-class="toggleWidthStart">
                                    <div v-if="isVisible" class="d-flex align-items-center position-relative"
                                        style="cursor:pointer;background: #232a2f;border-radius: 9999px;height:1.35rem;transition: width .25s ease, margin-left .25s ease;width: 3.25rem;margin-left: 1rem;"
                                        v-on:click="setSearchMode">
                                        <div class="position-absolute"
                                            :style="'background: #8ec73a;border-radius: 9999px;width:1.2rem;height:1.2rem;transition: all .25s;' +
                                            ((isSearchMode) ? 'left:31.5px;' : 'left:2px;')">
                                        </div>
                                        <div class="position-absolute"
                                            :style="((!isSearchMode) ? 'left:5px;color: #fff;' : 'left:5px;')"><i
                                                class="m-0 fa"
                                                :class="favorites.length ? 'fa-star' : 'fa-sitemap'"></i></div>
                                        <div class="position-absolute"
                                            :style="'left:35px;' + ((isSearchMode) ? 'color: #fff;' : '')"><i
                                                class="m-0 fa fa-search"></i></div>
                                    </div>
                                </transition>
                            </li>
                            <div v-show="isSearchMode && isVisible"
                                class="my-1 d-flex align-items-center position-relative"
                                style="padding:0.5rem 1.25rem;display:none;">
                                <input type="text" v-model="clusterSearch" v-on:keyup="searchForNetOrCluster"
                                    class="form-control" style="padding-left:2rem;"
                                    placeholder="{{ trans('view.Search_EnterKeyword') }} ..." aria-label="Search ..."
                                    aria-describedby="Search for Net or Cluster">
                                <i class="fa fa-search position-absolute" style="left:30px;"></i>
                            </div>
                            <template v-if="isVisible">
                                @if ($globalConfig->isAllNetsSidebarEnabled)
                                    <li v-if="!isSearchMode" id="network_overview" class="has-sub">
                                        <div class="recolor sidebar-element">
                                            <a href="{{ route('TreeErd.show', ['field' => 'all', 'search' => 1]) }}"
                                                style="max-height: 20px; white-space: nowrap;">
                                                <i class="mr-2 fa fa-sitemap"></i>
                                                <span>{{ trans('view.Menu_allNets') }}</span>
                                            </a>
                                        </div>
                                    </li>
                                @endif
                                <template v-for="netelement in loopNetElements" :key="netelement.id">
                                    <li v-if="!loadingSearch || !isSearchMode" class="has-sub">
                                        <div v-cloak class="recolor sidebar-element align-items-baseline"
                                            style="display: flex;padding: 0.5rem 1.25rem;">
                                            <template v-if="isSearchMode" style="cursor: pointer;">
                                                <a href="javascript:;">
                                                    <i v-if="loadingFavorites.includes(netelement.id)"
                                                        class="mr-2 caret-link fa fa-circle-o-notch fa-spin"></i>
                                                    <i v-else class="mr-2 caret-link fa"
                                                        :class="favorites.includes(netelement.id) ? 'fa-star' : 'fa-star-o'"
                                                        v-on:click="favorNetelement(netelement)"></i>
                                                </a>
                                                <a :href="'/admin/Tree/erd/' + (netelement.base_type_id == 1 ? 'net/' :
                                                    'cluster/') + netelement.id"
                                                    class="no-underline"
                                                    style="max-height: 20px; white-space: nowrap;flex:1;width:80%;"
                                                    v-on:click="setNetActive(netelement.id)">
                                                    <span v-text="netelement.name" :title="netelement.name"
                                                        class="d-block text-ellipsis"></span>
                                                </a>
                                            </template>
                                            <template v-else>
                                                <i v-on:mouseenter="setHover(netelement, true)"
                                                    v-on:mouseLeave="setHover(netelement, false)"
                                                    v-on:click="directFavor(netelement, $event)" class="mr-2 fa"
                                                    :class="netElementSearchHoverClass(netelement)"
                                                    style="text-decoration: none;"></i>
                                                <a :href="'/admin/Tree/erd/' + (netelement.base_type_id == 1 ? 'net/' :
                                                    'cluster/') + netelement.id"
                                                    class="no-underline caret-link d-flex"
                                                    style="max-height: 20px; white-space: nowrap;flex:1;width:80%;"
                                                    v-on:click="setNetActive(netelement.id)">
                                                    <span v-if="! store.minified" v-text="netelement.name"
                                                        :title="netelement.name" class="d-block text-ellipsis"></span>
                                                </a>
                                            </template>
                                            <a href="javascript:;" v-if="netelement.base_type_id == 1"
                                                v-on:click="loadCluster(netelement)" class="caret-link"
                                                style="cursor: pointer;width: 100%; text-align: right;">
                                                <i v-if="loadingClusters.includes(netelement.id)"
                                                    class="fa fa-circle-o-notch fa-spin"></i>
                                                <i
                                                    v-else-if="netelement.clustersLoaded && !netelement.clusters.length"></i>
                                                <i v-else class="fa fa-caret-right"
                                                    :class="{ 'fa-rotate-90': !netelement.isCollapsed && !store.minified }"
                                                    style="transition:all .25s;"></i>
                                            </a>
                                        </div>
                                        <transition 
                                            name="accordion" 
                                            v-on:before-enter="beforeEnter" 
                                            v-on:enter="enter"
                                            v-on:before-leave="beforeLeave" 
                                            v-on:leave="leave"
                                            v-on:after-leave="afterLeave"
                                        >
                                            <ul :id="'network_' + netelement.id"
                                                v-if="netelement.base_type_id == 1 && netelement.clustersLoaded && netelement.clusters.length && !netelement.isCollapsed"
                                                class="m-0 sidebar-hover p-b-10 p-l-20"
                                                :class="{
                                                    'minifiedMenu': (showMinifiedHoverNet && netelement
                                                        .clustersLoaded)
                                                }"
                                                :style="(!store.minified ? 'transition:max-height .3s linear;' : '') +
                                                'overflow:hidden;list-style-type: none;background: #1a2229;'">
                                                <template v-for="cluster in netelement.clusters" :key="cluster.id">
                                                    <li :id="'cluster_' + cluster.id"
                                                        v-on:click="setNetActive(cluster.id)"
                                                        :class="{
                                                            active: (clickedNetelement == cluster.id),
                                                            'p-t-10': (
                                                                netelement.clusters[0].id == cluster.id)
                                                        }">
                                                        <a :href="'/admin/Tree/erd/cluster/' + cluster.id"
                                                            style="display:flex;padding:5px 20px;color:#889097;overflow: hidden;white-space:nowrap;font-weight:300;text-decoration:none;width:95%;">
                                                            <i class="mr-2 fa fa-circle-thin text-info"></i>
                                                            <span v-text="cluster.name" :title="cluster.name"
                                                                class="d-block text-ellipsis"></span>
                                                        </a>
                                                    </li>
                                                </template>
                                            </ul>
                                        </transition>
                                    </li>
                                </template>
                                <li v-if="Object.keys(loopNetElements).length === 0 && !isSearchMode && !store.minified && netCount"
                                    class="m-l-20 m-t-10 text-light w-75">{{ trans('messages.refreshPage') }}
                                </li>
                                <li v-if="Object.keys(loopNetElements).length === 0 && !isSearchMode && !store.minified && !netCount"
                                    class="m-l-20 m-t-10 text-light w-75">{{ trans('messages.noNetElement') }}
                                </li>
                                <li v-if="Object.keys(loopNetElements).length === 0 && isSearchMode && clusterSearch.length && !loadingSearch"
                                    class="m-l-20 m-t-10 text-light w-75">{{ trans('messages.noClusterOrNet') }}
                                </li>
                                <li v-if="isSearchMode && clusterSearch.length && loadingSearch"
                                    class="text-center m-l-20 m-t-10 w-75"><i class="fa fa-circle-o-notch fa-spin"></i>
                                </li>
                            </template>
                        @endif
                    </ul>
                    <!-- end sidebar nav -->
            </div>
            <div class="absolute top-0 flex flex-col items-center w-5 h-full pt-2 space-y-6 bg-lime-nmsprime -right-5" :class="{'left-64': (menu == 'Access Network' && !store.minified) || (!store.minified && {{(int)!Module::collections()->has('CoreMon')}})}">
                <div v-cloak class="hover:cursor-pointer text-white" v-on:click="handleMinify">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 duration-300 ease-in-out" :class="{ 'rotate-180': !store.minified, 'rotate-0': store.minified }" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <div v-cloak v-if="!store.minified" class="hover:cursor-pointer text-white" v-on:click="pinSidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 duration-300 ease-in-out" :class="{ 'rotate-0': pinned, 'rotate-90 hover:rotate-0': !pinned }" fill="currentColor"
                        viewBox="0 0 384 512" stroke="none" stroke-width="2">
                        <path
                            d="M32 32C32 14.33 46.33 0 64 0H320C337.7 0 352 14.33 352 32C352 49.67 337.7 64 320 64H290.5L301.9 212.2C338.6 232.1 367.5 265.4 381.4 306.9L382.4 309.9C385.6 319.6 383.1 330.4 377.1 338.7C371.9 347.1 362.3 352 352 352H32C21.71 352 12.05 347.1 6.04 338.7C.0259 330.4-1.611 319.6 1.642 309.9L2.644 306.9C16.47 265.4 45.42 232.1 82.14 212.2L93.54 64H64C46.33 64 32 49.67 32 32zM224 384V480C224 497.7 209.7 512 192 512C174.3 512 160 497.7 160 480V384H224z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</sidebar>
