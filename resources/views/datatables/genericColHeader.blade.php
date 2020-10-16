<?php
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
