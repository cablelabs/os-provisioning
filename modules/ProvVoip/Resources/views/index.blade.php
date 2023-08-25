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

@section('dashboard')
    <div class="grid {{ $gap }} sm:grid-cols-12">
        <div class="sm:col-span-6 lg:col-span-4 2xl:col-span-3 wide:col-span-2">
            @include('provvoip::widgets.quickstart')
        </div>
        <div class="sm:col-span-12 lg:col-span-8 wide:col-span-4">
            @include('Generic.widgets.moduleDocu', [ 'urls' => [
                'documentation' => 'https://devel.nmsprime.com/confluence/display/NMS/VoIP',
                'youtube' => 'https://youtu.be/SxTsflcNeUQ',
                'forum' => 'https://devel.nmsprime.com/confluence/display/nmsprimeforum/VoIP',
            ]])
        </div>
    </div>
@stop
