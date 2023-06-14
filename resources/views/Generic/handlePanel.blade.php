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
<script language="javascript">
var handlePanel = function () {
    "use strict"
    var targetHandle = '.panel-heading'
    var connectedTarget = '.tab-pane'

    $('.tab-pane').sortable({
        handle: targetHandle,
        connectWith: connectedTarget,
        stop: function (event, ui) {
            ui.item.find('.panel-title').append('<i class="fa fa-refresh fa-spin m-l-5" data-id="title-spinner"></i>')
            handlePanelPosition(ui.item)
        }
    });

    // Note: panels with class col as parent are handled automatically via apps.js
};

var handlePanelPosition = function(element) {
    "use strict"
    if ($('.ui-sortable').length == 0) {
        return
    }

    var PanelObject = {}
    $.when($('.tab-pane').each(function() {
        let tabPanel = []
        let id = $(this)[0].id
        let relations = $(this).find('[data-sort-id]')

        if (relations.length == 0) {
            return
        }

        $.each(relations, ( index, relation) => {
            tabPanel.push(relation.getAttribute('data-sort-id'))
        })

        PanelObject[id] = tabPanel
    })).done(function() {
        var targetPage = "{!! isset($view_header) ? $view_header : '' !!}"
        localStorage.setItem(targetPage, JSON.stringify(PanelObject))
        $(element).find('[data-id="title-spinner"]').delay(500).fadeOut(500, function() {
            $(this).remove()
        });
    });
};

var loadPanelPositionFromStorage = function() {
    "use strict"
    if (typeof(Storage) == 'undefined' && typeof(localStorage) == 'undefined') {
       alert('Your browser is not supported with the local storage')
       return
    }

    var targetPage = "{!! isset($view_header) ? $view_header : '' !!}"
    var panelPositionData = localStorage.getItem(targetPage)

    if (panelPositionData) {
        panelPositionData = JSON.parse(panelPositionData)

        $.when($('.tab-pane').each(function() {
            var targetColumn = $(this)[0].id
            var storageData = panelPositionData[targetColumn]

            if (storageData) {
                $.each(storageData, function(index, data) {
                    var targetId = $('[data-sort-id="'+ data +'"]').not('[data-init="true"]')

                    if ($(targetId).length !== 0) {
                        prepareLivewireData($(targetId)[0])
                        var targetHtml = $(targetId).clone()
                        $(targetId).remove()
                        $('[id ="' + targetColumn + '"]').append(targetHtml)
                        $('[data-sort-id="'+ data.id +'"]').attr('data-init','true')
                    }
                })
            }
        })).done(function() {
            window.dispatchEvent(new CustomEvent('localstorage-position-loaded'))
            window.livewire?.rescan()
        });
    }
};

var prepareLivewireData = function (node){
    /**
    * On first load, livewire has it's components in initial state where all
    * data necessary to initialize a livewire component lives on an
    * attribute wire:initial-data as JSON.
    * On page load, JS part of livewire parses all the components and
    * Initialize them.
    * Before we re-arrange the panels, livewire has already run initialized the components.
    * All the necessary payload lives on the element (as object) after initialization.
    *
    * We need to bring the livewire components inside the target panel
    * to its initial state. We are selecting all the elements
    * having attribute wire:id because all livewire
    * components would have that set by Livewire, and setting
    * the wire:initial-data
    */
    node.querySelectorAll('[wire\\:id]').forEach(function(el) {
        const component = el.__livewire;
        if(component){
            const dataObject = {
                fingerprint: component.fingerprint,
                serverMemo: component.serverMemo,
                effects: component.effects,
            };
            el.setAttribute('wire:initial-data', JSON.stringify(dataObject));

            /**
            * tearDown function on the component added by Livewire
            * will un-register all the event handlers
            */
            component.tearDown()

            /**
            * Brining the livewire component to initial state won't make Livewire to re-initialize
            * it again. Livewire stores all the components' references (by ID from wire:id)
            * inside an internal Store (object). By default, we don't have
            * any helper/function from Livewire to delete that.
            * So, we are doing it manually using JS `delete` operator.
            * `livewire.components` property gives us that Store instance
            * on which `componentsById` stores all the references.
            *
            * Now, running `livewire.rescan()` after we are done with
            * panels arrangments, will re-initialize the desired
            * components
            */
            delete livewire.components.componentsById[component.id];
        }

    });
}

$(document).ready(function() {
    handlePanel()
    loadPanelPositionFromStorage()
});
</script>
