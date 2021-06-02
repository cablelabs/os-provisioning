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
@extends ('Layout.split84')

@section('content_top')

@stop

@section('content_left')
    @DivOpen(12)
        @if (isset($query))
            <h4>{{"$results ". \App\Http\Controllers\BaseViewController::translate_view('MatchesFor', 'Search')." '$query'" }}</h4>
            <hr>
        @endif
    @DivClose()
    @DivOpen(12)
        @if ($view_var->isNotEmpty())
            <table class="table table-hover datatable ClickableTd">
                <thead>
                    <tr>
                        <th>{{App\Http\Controllers\BaseViewController::translate_label('Type')}}</th>
                        <th>{{App\Http\Controllers\BaseViewController::translate_label('Entry')}}</th>
                        <th>{{App\Http\Controllers\BaseViewController::translate_label('Description')}}</th>
                    </tr>
                </thead>
                @foreach ($view_var as $object)
                    <?php
                        // TODO: move away from view!!
                        $modelName = class_basename(get_class($object));
                        if (!\Route::has($modelName.'.edit'))
                            continue;

                        if (is_array($object->view_index_label()))
                        {
                            $link = \HTML::linkRoute($modelName.'.edit', $object->view_index_label()['header'], $object->id);
                            $descr = $object->view_index_label()['header'];
                        }
                        else
                        {
                            $link = \HTML::linkRoute($modelName.'.edit', $object->view_index_label(), $object->id);
                            $descr = $object->view_index_label();
                        }
                    ?>
                    <tr class={{\App\Http\Controllers\BaseViewController::prep_index_entries_color($object)}}>
                        <td>{{$modelName}}</td>
                        <td>{{$link}}</td>
                        <td>{{$descr}}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    @DivClose()
@stop
