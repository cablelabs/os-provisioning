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

    /**
     * Shows the html links of the related objects recursivly
     * TODO: should be placed in a global concept and not on module base
     */
    $s = '';

    $model = $modem ?? $netgw;
    $parent = $model;
    $classname = explode('\\',get_class($parent));
    $classname = end($classname);
?>
<div class='flex'>
    <div class='flex flex-col py-1 !px-3 text-slate-100 rounded bg-slate-800 hover:bg-slate-900'>
        <a href="{{ route($classname.'.index')}}" class="text-white hover:text:white no-underline">
            {!! $model->view_icon() !!}{{ $classname }}
        </a>
    </div>
</div>
@php
    while ($parent)
    {
        $tmp   = explode('\\',get_class($parent));
        $view  = end($tmp);
        $icon  = $parent->view_icon();
        $label = is_array($ret = $parent->view_index_label()) ? $ret['header'] : $ret;
        $s =  "<div class='flex items-center'><div class='w-2 h-full rounded-full bg-".$parent->get_bsclass().
            "'></div><div class='flex flex-col px-2.5 text-black dark:text-slate-100'>".
                HTML::decode(HTML::linkRoute($view.'.edit', $icon.Str::limit($label, 40, '...'), $parent->id)).
                '</div></div>'.$s;

        $parent = $parent->view_belongs_to();

        if ($parent instanceof \Illuminate\Support\Collection) {
            $parent = $parent->first();
        }
    }
@endphp
{!! $s !!}
