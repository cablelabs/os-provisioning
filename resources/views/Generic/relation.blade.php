{{--
Relation Blade is used inside a Panel Element to display relational class objects on the right window side

@param array $relation: the relation array to be displayed, contains one element of $relations element from edit.blade
@param string $key: SQL table key, required for adding new elements with reference to $key table
@param string $class $view: the class of the object to be used.
--}}

{{-- Error Message --}}
@if (Session::get('delete_message') && Session::get('delete_message')['class'] == $class)
    <div id='delete_msg' class="note note-{{ Session::get('delete_message')['color'] }} fade in m-b-15">
        <strong><h5>{{ Session::get('delete_message')['message'] }} </h5></strong>
    </div>
@endif

@DivOpen(12)
    @if ($info)
        @if (strlen($info) < 200)
            <div class="alert alert-info fade show" style="padding-bottom: 0.5rem; padding-top: 0.5rem">
              <span class="close" data-dismiss="alert">×</span>
              {{ $info }}
            </div>
        @else
            <div class="col-md-1">
                <a data-toggle="popover" data-container="body" data-trigger="hover" title="Info" data-placement="right" data-content="{{ $info }}">
                    <i class="fa fa-2x p-t-5 fa-question-circle text-info"></i>
                </a>
            </div>
        @endif
    @endif

    <div class="row">
    @can('create', Session::get('models.'.$class))
        {{-- Create Button: (With hidden add fields if required) --}}
        @if (! isset($options['hide_create_button']))

            {!! Form::open(['url' => route($class.'.create', [$key => $view_var->id]), 'method' => 'POST', 'name' => 'createForm']) !!}
            {!! Form::hidden($key, $view_var->id) !!}

            {{-- Add hidden input fields if create tag is set in $form_fields - This sets global POST Variable --}}
            @foreach($form_fields as $field)
                @if (array_key_exists('create', $field) && in_array($class, $field['create']))
                    {!! Form::hidden($field["name"], $view_var->{$field["name"]}) !!}
                @endif
            @endforeach

            <div class="col align-self-start">
                <a href="{{ route($class.'.create', [$key => $view_var->id]) }}">
                    <button class="btn btn-outline-primary float-right m-b-10" style="simple" data-toggle="tooltip" data-delay='{"show":"250", "hide": 50}' data-placement="bottom" data-boundary="viewport"
                        title="{{ isset($options['create_button_text']) ? trans($options['create_button_text']) : \App\Http\Controllers\BaseViewController::translate_view('Create '.$class, 'Button') }}">
                        <i class="fa fa-plus fa-2x" aria-hidden="true"></i>
                    </button>
                </a>
            </div>

            {!! Form::close() !!}
        @endif
    @endcan
    @if($relation)
        @can('delete', $relation->get(0))
            {{-- Delete Button --}}
            @if (! isset($options['hide_delete_button']) && isset($relation[0]))
                <div class="col align-self-end">
                    <button class="btn btn-outline-danger m-b-10 float-right"
                            data-toggle="tooltip"
                            data-delay='{"show":"250"}'
                            data-placement="top"
                            form="{{$tab['name'].$class}}"
                            style="simple"
                            title="{{ !isset($options['delete_button_text']) ? \App\Http\Controllers\BaseViewController::translate_view('Delete', 'Button') : trans($options['delete_button_text']) }}">
                                <i class="fa fa-trash-o fa-2x" aria-hidden="true"></i>
                    </button>
                </div>
            @endif
        @endcan
    @endif
    </div>
@DivClose()

{{-- The Relation Table --}}
@if (isset($relation[0]))
@DivOpen(12)
    @if (isset($options['many_to_many']))
        {!! Form::open(array('route' => array($route_name.'.detach', $view_var->id, $options['many_to_many']), 'method' => 'post', 'id' => $class)) !!}
    @else
        {!! Form::open(array('route' => array($class.'.destroy', 0), 'method' => 'delete', 'id' => $tab['name'].$class)) !!}
    @endif

    <table class="table">
        @foreach ($relation as $rel_elem)
            <?php $labelData = $rel_elem->view_index_label(); ?>
            <tr class="{{isset ($labelData['bsclass']) ? $labelData['bsclass'] : ''}}">
                <td width="20"> {!! Form::checkbox('ids['.$rel_elem->id.']', 1, null, null, ['style' => 'simple']) !!} </td>
                <td> {!! $rel_elem->view_icon() !!} {!! HTML::linkRoute($class.'.'.$method, is_array($labelData) ? $labelData['header'] : $labelData, $rel_elem->id) !!} </td>
            </tr>
        @endforeach
    </table>

    {!! Form::close() !!}
@DivClose()
@endif
