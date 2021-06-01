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
{{-- https://laracasts.com/discuss/channels/laravel/categories-tree-view/replies/114604 --}}

<?php   $color_classes = ['default-1', 'default-2', 'default-3', 'default-4']; // default
?>

<ul>
@foreach($items as $key => $item)
    @if (gettype($item) == 'object')
        <?php
            $type = method_exists($item, 'get_icon_type') ? $item->get_icon_type() : $color_classes[$color % 4];
        ?>
        <li id="ids[{{$item->id}}]"
            class="f-s-14 p-t-5 {{in_array($item->id, $undeletables) ? 'nocheck' : ''}}
                {{ in_array($item->id, $undeletables) && $item->parent_id ? 'p-l-25' : ''}}"
            data-jstree='{"type":"{!! $type !!}" }'>

            {!! HTML::linkRoute("$route_name.edit", $item->view_index_label(), $item->id) !!}

            @if($item->children->count() > 0)
                @include('Generic.tree_item', array('items' => $item->children, 'color' => $color++))
            @else
                <?php $color++; ?>
            @endif
        </li>
    @else
        <li class="f-s-14 p-t-5 nocheck" data-jstree='{"type":"default-1" }'>
        @if(is_array($item))
            {{$key}}
            @include('Generic.tree_item', array('items' => $item))
        @else
            {!! HTML::linkRoute('Modem.index', "$key: $item", ['show_filter' => 'sw_rev', 'data' => $key]) !!}
        @endif
    @endif
@endforeach
</ul>
