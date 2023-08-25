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
@extends ('Layout.split-nopanel')

@section ('content_left')
    <div>

        <div class="flex justify-center pb-6">
            <h1 class="text-3xl">NMS Prime Apps</h1>
        </div>

        <h2 class="text-xl">{{ trans('messages.nativeApps') }}</h2>
        @foreach ($nativeApps as $nativeApp)
            <div class="grid grid-flow-row-dense grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 wide:grid-cols-7 py-6 {{ $gap }}">
                @foreach ($nativeApp as $category => $modules)
                @php
                    $moduleCount = count($modules);
                    $colSpan = match($moduleCount) {
                        1 => 'col-span-1',
                        2 => 'col-span-2',
                        3 => 'col-span-2 sm:col-span-3 lg:col-span-3',
                        4 => 'col-span-2 sm:col-span-3 lg:col-span-4',
                        5 => 'col-span-2 sm:col-span-3 lg:col-span-4 xl:col-span-5',
                        default => 'col-span-2 sm:col-span-3 lg:col-span-4 xl:col-span-5 wide:col-span-7',
                    };
                    $gridCols = match($moduleCount) {
                        1 => 'grid-cols-1',
                        2 => 'grid-cols-2',
                        3 => 'grid-cols-2 sm:grid-cols-3',
                        4 => 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-4',
                        5 => 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5',
                        default => 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 wide:grid-cols-7',
                    };
                @endphp
                    <div class="bg-white shadow-md p-4 {{ $colSpan }}">
                        <div>
                            <h3 class="mb-2 text-lg">{{ $category }}</h3>
                        </div>
                        <div class="grid grid-flow-row {{ $gridCols }} {{ $gap }}">
                            @foreach ($modules as $attr)
                                <div class="flex flex-col items-center justify-center">
                                    <a href="{{ $attr['link'] }}">
                                        <img class="h-28"
                                            src="{{ asset('images/apps/'.$attr['icon']) }}"
                                            title="{{ trans('view.'.$attr['description']) }}"
                                        >
                                    </a>
                                    <div class="dark:text-slate-100 !pt-5 font-semibold text-center">{{ $attr['name'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
        </div>

        <h2 class="text-xl ">{{ trans('messages.externalApps') }}</h2>
        <div class="grid grid-flow-row-dense grid-cols-2 py-6 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 wide:grid-cols-7 {{ $gap }}">
            @foreach ($externalApps as $name => $externalApp)
                @if (Route::currentRouteName() == 'Apps.'.$externalApp['state'] && file_exists(public_path('images/'.$externalApp['icon'])))
                    <div class="grid p-4 bg-white shadow-md place-content-center">
                        <div class="flex flex-col items-center justify-center">
                            <a href="{{ $externalApp['state'] == 'active' ? $externalApp['link'] : $externalApp['website'] }}">
                                <img class="h-28"
                                    title="{{ trans('view.'.$externalApp['description']) }}"
                                    src="{{ asset('images/'.$externalApp['icon']) }}"
                                >
                            </a>
                            <div class="dark:text-slate-100 !pt-5 font-semibold">{{ $name }}</div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@stop
