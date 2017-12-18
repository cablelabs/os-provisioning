{{-- Add Columns - First 2 Columns are for Responsive Button and Input Checkbox --}}
columns:[
            {data: 'responsive', orderable: false, searchable: false},
    @if (isset($delete_allowed) && $delete_allowed == true)
            {data: 'checkbox', orderable: false, searchable: false},
    @endif
    @if (isset($model->view_index_label()['index_header']))
        @foreach ($model->view_index_label()['index_header'] as $field)
            @if ( starts_with($field, $model->view_index_label()["table"].'.'))
                {data: '{{ substr($field, strlen($model->view_index_label()["table"]) + 1) }}', name: '{{ $field }}'},
            @else
                {data: '{{ $field }}', name: '{{$field}}',
                searchable: {{ isset($model->view_index_label()["sortsearch"][$field]) ? "false" : "true" }},
                orderable:  {{ isset($model->view_index_label()["sortsearch"][$field]) ? "false" : "true" }}
                },
            @endif
        @endforeach
    @endif
],
{{-- Set Sorting if order_by is set -> Standard is ASC of first Column --}}
@if (isset($model->view_index_label()['order_by']))
    order:
    @foreach ($model->view_index_label()['order_by'] as $columnindex => $direction)
        @if (isset($delete_allowed) && $delete_allowed == true)
            [{{$columnindex + 2}}, '{{$direction}}'],
        @else
            [{{$columnindex + 1}}, '{{$direction}}'],
        @endif
    @endforeach
@endif
