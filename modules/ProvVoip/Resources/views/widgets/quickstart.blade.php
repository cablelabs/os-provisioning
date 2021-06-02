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
<style>
    a:hover {
        text-decoration: none;
    }
</style>

<div class="widget widget-stats bg-grey">
    {{-- info/data --}}
    <div class="stats-info text-center">
        {!! HTML::decode (HTML::linkRoute('PhoneTariff.create',
            '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
                <i style="font-size: 25px;" class="img-center fa fa-phone-square p-10"></i><br />
                <span class="username text-ellipsis text-center">'.trans_choice('view.Button_Create PhoneTariff', 1).'</span>
            </span>'))
        !!}
    </div>
    {{-- reference link --}}
    <div class="stats-link noHover"><a href="#">{{ trans('view.dashboard.quickstart') }}</a></div>
</div>
