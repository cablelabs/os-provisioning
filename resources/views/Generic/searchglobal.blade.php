{{--

@param $query: the string query to search for
@param $scope: scope means context to search in (all | model name, like contract)

--}}

@extends ('Layout.split84')

@section('content_top')

    <h4><?php echo \App\Http\Controllers\BaseViewController::translate_view('GlobalSearch', 'Header'); ?></h4>
@stop

@section('content_left')


    @DivOpen(12)
        @if (isset($query))
            <h4><?php echo \App\Http\Controllers\BaseViewController::translate_view('MatchesFor', 'Search'); ?><tt>'{{ $query}}'</tt></h4>
            <hr>
        @endif
    @DivClose()

    @DivOpen(12)
        <table class="table table-hover datatable ClickableTd">
            <thead>
                <tr>
                    <th></th>
                    <th>{{App\Http\Controllers\BaseViewController::translate_label('Type')}}</th>
                    <th>{{App\Http\Controllers\BaseViewController::translate_label('Entry')}}</th>
                    <th>{{App\Http\Controllers\BaseViewController::translate_label('Description')}}</th>
                </tr>
            </thead>

            @foreach ($view_var as $object)
                <?php
                    // TODO: move away from view!!
                    $cur_model_parts = explode('\\', get_class($object));
                    $cur_model = array_pop($cur_model_parts);

                    if (!\Route::has($cur_model.'.edit'))
                        continue;

                    if (is_array($object->view_index_label()))
                    {
                        $link = \HTML::linkRoute($cur_model.'.edit', $object->view_index_label()['header'], $object->id);
                        $descr = $object->view_index_label()['header'];
                    }
                    else
                    {
                        $link = \HTML::linkRoute($cur_model.'.edit', $object->view_index_label(), $object->id);
                        $descr = $object->view_index_label();
                    }
                ?>

                <tr class={{\App\Http\Controllers\BaseViewController::prep_index_entries_color($object)}}>
                    <td>{{Form::checkbox('ids['.$object->id.']', 1, null, null, ['style' => 'simple'])}}</td>
                    <td>{{$cur_model}}</td>
                    <td>{{$link}}</td>
                    <td>{{$descr}}</td>
                </tr>
            @endforeach
        </table>
    @DivClose()

@stop
