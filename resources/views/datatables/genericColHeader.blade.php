{{-- Add Columns - First 2 Columns are for Responsive Button and Input Checkbox --}}
columns:[
            {data: 'responsive', orderable: false, searchable: false},
    @if (isset($delete_allowed) && $delete_allowed == true)
            {data: 'checkbox', orderable: false, searchable: false},
    @endif
    @if (isset($view_var->view_index_label()['index_header']))
        @foreach ($view_var->view_index_label()['index_header'] as $field)
            @if ( starts_with($field, $view_var->view_index_label()["table"].'.'))
                {data: '{{ substr($field, strlen($view_var->view_index_label()["table"]) + 1) }}', name: '{{ $field }}'},
            @else
                {data: '{{ $field }}', name: '{{$field}}',
                searchable: {{ isset($view_var->view_index_label()["sortsearch"][$field]) ? "false" : "true" }},
                orderable:  {{ isset($view_var->view_index_label()["sortsearch"][$field]) ? "false" : "true" }}
                },
            @endif
        @endforeach
    @endif
],
{{-- Set Sorting if order_by is set -> Standard is ASC of first Column --}}
@if (isset($view_var->view_index_label()['order_by']))
    order:
    @foreach ($view_var->view_index_label()['order_by'] as $columnindex => $direction)
        @if (isset($delete_allowed) && $delete_allowed == true)
            [{{$columnindex + 2}}, '{{$direction}}'],
        @else
            [{{$columnindex + 1}}, '{{$direction}}'],
        @endif
    @endforeach
@endif
