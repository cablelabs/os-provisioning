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

    $index_label_arr = $model->view_index_label();
?>

{{-- Add Columns - First 2 Columns are for Responsive Button and Input Checkbox --}}
columns:[
        {data: 'responsive', orderable: false, searchable: false},
    @if (isset($delete_allowed) && $delete_allowed == true)
        {data: 'checkbox', orderable: false, searchable: false},
    @endif
    @if (isset($index_label_arr['index_header']))
        @foreach ($index_label_arr['index_header'] as $field)
        {
            @if ( Str::startsWith($field, $index_label_arr["table"].'.'))
                data: '{{ substr($field, strlen($index_label_arr["table"]) + 1) }}',
            @else
                data: '{{ $field }}',
            @endif
            name: '{{ $field }}',
            searchable: {{ isset($index_label_arr["disable_sortsearch"][$field]) ? $index_label_arr["disable_sortsearch"][$field] : "true" }},
            orderable:  {{ isset($index_label_arr["disable_sortsearch"][$field]) ? $index_label_arr["disable_sortsearch"][$field] : "true" }}
        },
        @endforeach
    @endif
],

{{-- Set Sorting if order_by is set -> Standard is ASC of first Column --}}
@if (isset($index_label_arr['order_by']))
    order:
    @foreach ($index_label_arr['order_by'] as $columnindex => $direction)
        @if (isset($delete_allowed) && $delete_allowed == true)
            [{{$columnindex + 2}}, '{{$direction}}'],
        @else
            [{{$columnindex + 1}}, '{{$direction}}'],
        @endif
    @endforeach
@endif
