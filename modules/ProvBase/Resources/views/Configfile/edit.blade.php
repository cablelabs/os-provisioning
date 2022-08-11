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
@extends ('Generic.edit')

@section('content_left')
    @include ('Generic.logging')
    <?php
        $blade_type = 'relations';
    ?>

    @include('Generic.above_infos')
    {!! Form::model($view_var, ['route' => [$form_update, $view_var->id], 'method' => 'put', 'files' => true, 'id' => 'EditForm']) !!}

    @include($form_path, $view_var)

@if (multi_array_key_exists(['lists'], $additional_data))
<div
    id="provbase-config-file-edit"
    class="dragdropfield"
    data-button-search="{{ trans('view.Button_Search') }}"
    data-header-translations="{{ trans('view.configfile') }}"
    data-configfile-drag-drop-threshholds-critical-orange="{{ trans('view.configfile.dragdrop.threshholds', ['severity' => trans('view.critical'), 'color' => trans('view.orange')]) }}"
    data-configfile-drag-drop-threshholds-warning-yellow="{{ trans('view.configfile.dragdrop.threshholds', ['severity' => trans('view.warning'), 'color' => trans('view.yellow')]) }}"
    data-configfile-drag-drop-threshholds-success-green="{{ trans('view.configfile.dragdrop.threshholds', ['severity' => trans('view.success'), 'color' => trans('view.green')]) }}"
    data-route-configfile-refresh-genie-acs="{{ route('Configfile.refreshGenieAcs', $view_var->id ) }}"
    data-route-configfile-search-device-params="{{ route('Configfile.searchDeviceParams', $view_var->id) }}"
    data-lists='@json($additional_data["lists"])'
    data-columns='@json($additional_data["columns"])'
>
</div>
@endif
@stop

@section('javascript_extra')
@if (multi_array_key_exists(['lists'], $additional_data))
<script src="{{asset('components/assets-admin/plugins/sortable/Sortable.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/vuedraggable/dist/vuedraggable.umd.min.js')}}"></script>
<script>

// hide coloring related inputs if checkbox not checked
$(document).ready(function()
{
    $('input[class=toggleColorizeParams]').each(function () {
        if (!$(this).is(':checked')) {$(this).next().toggleClass('d-none')};
    });
});
</script>
@endif
@stop
