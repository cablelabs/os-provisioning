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
{{--

@param $headline: the link header description in HTML

@param $view_var: the object we are editing
@param $form_update: the update route which should be called when clicking save
@param $form_path: the form view to be displayed inside this blade (mostly Generic.form)
@param $tabs: the page hyperlinks returned from analysisPage() or prep_right_panels()
@param $relations: the relations array() returned by prep_right_panels() in BaseViewController

--}}
@extends ('Layout.split-nopanel')

@section('content_top')

    {!! $headline !!}

@stop


@section('content_left')
    @include ('Generic.logging')
    <?php
        $blade_type = 'relations';
    ?>

    @include('Generic.above_infos')
    {!! Form::model($view_var, ['route' => [$form_update, $view_var->id], 'method' => 'put', 'files' => true, 'id' => 'EditForm']) !!}

        @include($form_path, $view_var)

    {{ Form::close() }}
@stop


@section('content_right')
    @if(isset($relations) && !empty($relations))
        <div class="col-lg-{{isset($edit_right_md_size) ? $edit_right_md_size : 4}}">
            <div class="tab-content bg-gray-100 dark:bg-slate-900">
                @foreach ($tabs as $tab)
                    @if (isset($relations[$tab['name']]))
                        <div id="{{ $tab['name'] }}"
                            v-show="tabStates['{{ $tab['name'] }}']"
                            :class="{'active': tabStates['{{ $tab['name'] }}']}"
                            class="tab-pane {{ $firstTab == $tab['name'] ? 'active' : ''}}">
                            @foreach($relations[$tab['name']] as $view => $relation)
                                @if ($view === 'icon')
                                    @continue
                                @endif

                                {{-- The section content for the new Panel --}}
                                @section($tab['name'].$view)
                                    @if (is_array($relation))

                                        {{-- include pure HTML --}}
                                        @if (isset($relation['html']))
                                            {!! $relation['html'] !!}
                                        @endif

                                        {{-- include a view --}}
                                        @if (isset($relation['view']))
                                            @if (is_string($relation['view']))
                                                @include ($relation['view'])
                                            @endif
                                            @if (is_array($relation['view']))
                                                @include ($relation['view']['view'], $relation['view']['vars'] ?? [])
                                                <?php $md_size = isset($relation['view']['vars']['md_size']) ?? null; ?>
                                            @endif
                                        @endif

                                        {{-- include a relational class/object/table, like Contract->Modem --}}
                                        @if (isset($relation['class']) && array_key_exists('relation', $relation))
                                            @include('Generic.relation', [
                                                'count' => $relation['count'] ?? 0,
                                                'relation' => $relation['relation'],
                                                'class' => $relation['class'],
                                                'info' => $relation['info'] ?? '',
                                                'key' => strtolower($view_var->table).'_id',
                                                'method' => $relation['method'] ?? 'edit',
                                                'options' => isset($relation['options']) ? ($relation['options']) : null,
                                            ])
                                        @endif

                                    @endif
                                @stop

                                {{-- The Bootstap Panel to include --}}
                                @include ('bootstrap.panel', [
                                    'content' => $tab['name'].$view,
                                    'view_header' => \App\Http\Controllers\BaseViewController::translate_view($view, 'Header' , 2),
                                    'options' => $relation['panelOptions'] ?? null,
                                    'handlePanelPosBy' => 'nmsprime',
                                    ])

                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Alert --}}
    @if (Session::has('alert'))
        @foreach (Session::get('alert') as $notif => $message)
            @include('bootstrap.alert', array('message' => $message, 'color' => $notif))
            <?php Session::forget("alert.$notif"); ?>
        @endforeach
    @endif

@stop

@section('javascript')
@if(isset($tabs))
@include('Generic.js.logging')
@include('Generic.handlePanel')
@endif
@stop
