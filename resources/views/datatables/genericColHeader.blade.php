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

{{-- Add Columns - First 2 Columns are for Responsive Button and Input Checkbox --}}
columns:[
    @if (isset($delete_allowed) && $delete_allowed == true)
        {data: 'checkbox', orderable: false, searchable: false},
    @endif
    @if (isset($indexTableInfo['index_header']))
        @foreach ($indexTableInfo['index_header'] as $field)
        {
            @if ( Str::startsWith($field, $indexTableInfo["table"].'.'))
                data: '{{ substr($field, strlen($indexTableInfo["table"]) + 1) }}',
            @else
                data: '{{ $field }}',
            @endif
            name: '{{ $field }}',
            searchable: {{ isset($indexTableInfo["disable_sortsearch"][$field]) ? $indexTableInfo["disable_sortsearch"][$field] : "true" }},
            orderable:  {{ isset($indexTableInfo["disable_sortsearch"][$field]) ? $indexTableInfo["disable_sortsearch"][$field] : "true" }}
        },
        @endforeach
    @endif
],
