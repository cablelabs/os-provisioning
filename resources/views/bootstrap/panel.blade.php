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

    $style = 'height:100%';
    if (isset($height))
        $style = ($height == 'auto') ? '' : "height:$height%";

    $overflow_y = isset($overflow) ? $overflow : 'auto';

    $display = isset($options['display']) ? 'display: '.$options['display'] : '';

    $dataSortId = isset($tab) ? $tab['name'].'-'.$view : ($i ?? 1);
    $attrExt = isset($handlePanelPosBy) && $handlePanelPosBy == 'nmsprime' ? '' : 'able';
?>

{{-- begin col-dyn --}}
@if(isset($md))
<div class="col-{{ $md }}">
@endif
    <div class="panel panel-inverse card-2 dark:shadow-none dark:border-none dark:p-2 dark:bg-primary-dark" data-sort{{$attrExt}}-id="{{ $dataSortId }}">
        @include ('bootstrap.panel-header', ['view_header' => $view_header])
        <div class="panel-body fader text-gray-dark dark:bg-black-dark dark:mx-2" style="overflow-x: hidden; overflow-y:{{ $overflow_y }}; {{ $style }}; {{ $display }}">
            @yield($content)
        </div>
    </div>
@if(isset($md))
</div>
@endif
