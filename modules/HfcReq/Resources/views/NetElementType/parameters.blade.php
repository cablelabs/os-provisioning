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
@php
    $description['NetElementType'] = 'Parameter';
    $description['Parameter'] = 'SubOIDs';

    $model = NamespaceController::get_route_name();
@endphp

<div class="row justify-content-between">
    {{-- attach button --}}
    <div>
        {!! Form::open(['route' => [$model.'.assign', $view_var->id], 'method' => 'get']) !!}
            {!! Form::submit(trans('view.Assign', ['model' => $description[$model]]), ['style' => 'simple']) !!}
        {{-- .\App\Http\Controllers\BaseViewController::translate($view) --}}
        {!! Form::close() !!}
    </div>

    {{-- detach all button --}}
    <div>
        {!! Form::open(['route' => [$model.'.detach_all', $view_var->id], 'method' => 'delete']) !!}
            {!! Form::submit(trans('view.Detach all', ['model' => $description[$model]]), ['class' => 'btn btn-danger', 'style' => 'simple']) !!}
        {!! Form::close() !!}
    </div>

    {{-- Delete Button --}}
    @if (isset($list[0]) && ! isset($options['hide_delete_button']))
        @can('delete', $list[0])
            <div>
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
        @endcan
    @endif
</div>

@include('Generic.relationTable', [
    'class' => 'Parameter',
    'count' => 0, // set count to zero to not load Parameters by ajax again
    'relation' => $list,
    'method' => 'edit',
    'tab' => ['name' => 'Parameters'],
])
