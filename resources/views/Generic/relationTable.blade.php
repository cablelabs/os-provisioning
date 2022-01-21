@if (isset($relation[0]))
@DivOpen(12)
    @if (isset($options['many_to_many']))
        {!! Form::open(array('route' => array($route_name.'.detach', $view_var->id, $options['many_to_many']), 'method' => 'post', 'id' => $class)) !!}
    @else
        {!! Form::open(array('route' => array($class.'.destroy', 0), 'method' => 'delete', 'id' => $tab['name'].$class)) !!}
    @endif

    @if ($count < config('datatables.relationThreshhold'))
        <table class="table">
            @foreach ($relation as $rel_elem)
                <?php $labelData = $rel_elem->view_index_label(); ?>
                <tr class="{{isset ($labelData['bsclass']) ? $labelData['bsclass'] : ''}}">
                    <td width="20"> {!! Form::checkbox('ids['.$rel_elem->id.']', 1, null, null, ['style' => 'simple']) !!} </td>
                    <td> {!! $rel_elem->view_icon() !!} {!! HTML::linkRoute($class.'.'.$method, is_array($labelData) ? $labelData['header'] : $labelData, $rel_elem->id) !!} </td>
                </tr>
            @endforeach
        </table>
    @else
        @php
            $dtName = strtolower($tab['name']).$class.'Datatable';
        @endphp
        <table id="{{ $dtName }}" class="table table-hover datatable table-bordered d-table w-100">
            <thead>
                <tr>
                    <th class="w-5"></th>
                    <th class="w-100">Label</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let {{ $dtName }} = $('#{{ $dtName }}').DataTable({
                    @include('datatables.lang')
                    dom: 'rt'+"<'row'<'col-12 pb-1'i><'col-9 ml-auto'p>>",
                    columnDefs: [
                        {
                            defaultContent: "",
                            targets: "_all"
                        }
                    ],
                    {{-- AJAX CONFIGURATION --}}
                    processing: true, {{-- show loader--}}
                    serverSide: true, {{-- enable Serverside Handling--}}
                    deferRender: true,
                    ajax: '{{ route((new ReflectionClass($view_var))->getShortName().'.relationDatatable', ['model' => $view_var->id, 'relation' => $class]) }}',
                    columns:[
                        {data: 'checkbox', orderable: false, searchable: false},
                        {data: 'label', orderable: false, searchable: false}
                    ]
                })
            });
        </script>
    @endif
    {!! Form::close() !!}
@DivClose()
@elseif (isset($options['empty_message']))

<div class="text-dark" style="padding: 1.5rem;font-weight:bold;">
    {{ $options['empty_message'] }}
</div>

@endif
