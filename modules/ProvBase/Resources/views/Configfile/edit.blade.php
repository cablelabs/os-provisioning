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
    data-header-drag-drop="{{ trans('view.Header_DragDrop') }}"
    data-header-drag-drop-infotext="{{trans('view.Header_DragDrop Infotext')}}"
    data-header-drag-drop-infoheader="{{trans('view.Header_DragDrop Infoheader')}}"
    data-header-drag-drop-listname="{{ trans('view.Header_DragDrop Listname') }}"
    data-header-drag-drop-addlist="{{ trans('view.Button_DragDrop AddList') }}"
    data-configfile-dragdrop-listtype-list="{{ trans('view.configfile.dragdrop.listtype.list') }}"
    data-configfile-dragdrop-listtype-table="{{ trans('view.configfile.dragdrop.listtype.table') }}"
    data-configfile-dragdrop-listtype-paginated="{{ trans('view.configfile.dragdrop.listtype.paginated') }}"
    data-button-drag-drop-deleteList="{{ trans('view.Button_DragDrop DeleteList') }}"
    data-button-drag-drop-move-to="{{ trans('view.Button_DragDrop MoveTo') }}"
    data-button-drag-drop-move-to-new-list="trans('view.Button_DragDrop MoveToNewList')"
    data-button-drag-drop-delete-element="{{ trans('view.Button_DragDrop DeleteElement') }}"
    data-configfile-drag-drop-display-name="{{ trans('view.configfile.dragdrop.displayName') }}"
    data-configfile-drag-drop-display-name-placeholder="{{ trans('view.configfile.dragdrop.displayNamePlaceholder') }}"
    data-configfile-drag-drop-analysis-operator="{{ trans('view.configfile.dragdrop.analysisOperator') }}"
    data-configfile-drag-drop-operator-placeholder="{{ trans('view.configfile.dragdrop.operatorPlaceholder') }}"
    data-configfile-drag-drop-add="{{ trans('view.configfile.dragdrop.add') }}"
    data-configfile-drag-drop-sustract="{{ trans('view.configfile.dragdrop.sustract') }}"
    data-configfile-drag-drop-multiply="{{ trans('view.configfile.dragdrop.multiply') }}"
    data-configfile-drag-drop-divide="{{ trans('view.configfile.dragdrop.divide') }}"
    data-configfile-drag-drop-modulo="{{ trans('view.configfile.dragdrop.modulo') }}"
    data-configfile-drag-drop-analysis-operand="{{ trans('view.configfile.dragdrop.analysisOperand') }}"
    data-configfile-drag-drop-analysis-operand-placeholder="{{ trans('view.configfile.dragdrop.analysisOperandPlaceholder') }}"
    data-configfile-drag-drop-monitor-in-diagram="{{ trans('view.configfile.dragdrop.monitorInDiagram') }}"
    data-configfile-drag-drop-diagram-column="{{ trans('view.configfile.dragdrop.diagramColumn') }}"
    data-configfile-drag-drop-diagram-column-placeholder="{{ trans('view.configfile.dragdrop.diagramColumnPlaceholder') }}"
    data-configfile-drag-drop-diagram-operator="{{ trans('view.configfile.dragdrop.diagramOperator') }}"
    data-configfile-drag-drop-diagram-operand="{{ trans('view.configfile.dragdrop.diagramOperand') }}"
    data-configfile-drag-drop-diagram-operand-placeholder="{{ trans('view.configfile.dragdrop.diagramOperandPlaceholder') }}"
    data-configfile-drag-drop-colorize="{{ trans('view.configfile.dragdrop.colorize') }}"
    data-configfile-drag-drop-threshholds-critical-orange="{{ trans('view.configfile.dragdrop.threshholds', ['severity' => trans('view.critical'), 'color' => trans('view.orange')]) }}"
    data-configfile-drag-drop-threshholds-warning-yellow="{{ trans('view.configfile.dragdrop.threshholds', ['severity' => trans('view.warning'), 'color' => trans('view.yellow')]) }}"
    data-configfile-drag-drop-threshholds-success-green="{{ trans('view.configfile.dragdrop.threshholds', ['severity' => trans('view.success'), 'color' => trans('view.green')]) }}"
    data-configfile-drag-drop-select-map-parameter="{{ trans('view.configfile.dragdrop.selectMapParameter') }}"
    data-header-drag-drop-device-parameters="{{ trans('view.Header_DragDrop DeviceParameters') }}"
    data-button-drag-drop-refresh="{{ trans('view.Button_DragDrop Refresh') }}"
    data-route-configfile-refresh-genie-acs="{{ route('Configfile.refreshGenieAcs', $view_var->id ) }}"
    data-route-configfile-search-device-params="{{ route('Configfile.searchDeviceParams', $view_var->id) }}"
    data-button-search="{{ trans('view.Button_Search') }}"
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
