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
<script src="{{asset('components/assets-admin/plugins/vue/dist/vue.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/sortable/Sortable.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/vuedraggable/dist/vuedraggable.umd.min.js')}}"></script>

<div id="app" class="dragdropfield">
    <h2>{{ trans('view.Header_DragDrop') }}
        <a data-toggle="popover" data-html="true" data-container="body" data-trigger="hover" title="" data-placement="right"
            data-content="{{trans('view.Header_DragDrop Infotext')}}"
            data-original-title="{{trans('view.Header_DragDrop Infoheader')}}">
            <i class="fa fa-2x p-t-5 fa-question-circle text-info dragdropinfo"></i>
        </a>
    </h2>

    <div class="box" id="left">
        <draggable v-model="lists" :group="{ name: 'g1' }" class="droplist" :options="{draggable: '.list-group', filter: 'input', preventOnFilter: false}">
            <div v-for="(list, key) in lists" v-if="key != '0'" class="list-group">
                <div class="listbox">
                    <div class="h">
                        <input type="text" v-model="list.name">
                        <button class="btn btn-primary" @click="delList(key)">{{ trans('view.Button_DragDrop DeleteList') }}</button>
                    </div>
                    <draggable v-model="list.content" :group="{ name: 'g2' }" class="dropzone" :options="{draggable: '.dragdroplistitem', filter: 'input', preventOnFilter: false}">
                        <div class="dragdroplistitem" v-for="(item, id) in list.content" :key="item.id">
                            <div :class="item.id">@{{ item.id }} <i class="fa fa-cog dragdropitembutton" aria-hidden="true" v-on:click="itemmenu($event.target, key, id)"></i>
                            <div class="dragdropitemmenubox">
                              <span class="dragdropitemmenubutton" v-for="(listname, listkey) in lists" v-if="listkey != '0' && listkey !=  key" v-on:click="moveItem(key,listkey, id)">{{ trans('view.Button_DragDrop MoveTo') }} @{{ listname.name }}</span>
                                <span class="dragdropitemmenubutton" v-on:click="moveItem(key, -1, id)">{{ trans('view.Button_DragDrop MoveToNewList') }}</span>
                                <span class="dragdropitemmenubutton" v-on:click="moveItem(key, 0, id)">{{ trans('view.Button_DragDrop DeleteElement') }}</span>
                            </div>
                            <input type="text" name="oname" v-model="item.name"/>
                            <select name="calcOp" v-model="item.calcOp" v-dispatchsel2>
                                <option value=""></option>
                                <option value="+">+</option>
                                <option value="-">-</option>
                                <option value="*">*</option>
                                <option value="/">/</option>
                                <option value="%">%</option>
                            </select>
                            <input type="number" step="0.0001" name="calcVal" v-model="item.calcVal"/>
                            <select name="diagramVar" v-model="item.diagramVar" v-dispatchsel2>
                                <option value=""></option>
                                @foreach ($additional_data['columns'] as $column)
                                    <option value="{{$column}}">{{$column}}</option>
                                @endforeach
                            </select>
                            <select name="diagramOp" v-model="item.diagramOp" v-dispatchsel2>
                                <option value=""></option>
                                <option value="+">+</option>
                                <option value="-">-</option>
                                <option value="*">*</option>
                                <option value="/">/</option>
                                <option value="%">%</option>
                            </select>
                            <input type="number" step="0.0001" name="diagramVal" v-model="item.diagramVal"/>
                            </div>
                        </div>
                    </draggable>
                </div>
            </div>
        </draggable>

        <div class="newlist">
            <input type="text" v-model="listName" placeholder="{{ trans('view.Header_DragDrop Listname') }}" />
            <button class="btn btn-primary" @click="addList()">{{ trans('view.Button_DragDrop AddList') }}</button>
        </div>
    </div>

    <div class="box" id="right">
        <div :group="{ name: 'g1' }" class="droplist" >
            <div v-for="(list, key) in lists" v-if="key == '0'" class="list-group">
                <div class="listbox">
                    <div class="h">
                        <input type="text" value="{{ trans('view.Header_DragDrop DeviceParameters') }}" readonly="true"> <a href="{{route('Configfile.refreshGenieAcs', $view_var->id )}}" class="btn btn-primary" >{{ trans('view.Button_DragDrop Refresh') }}</a> 
                    </div>
                    <input type="text" v-on:keyup="ddFilter" id="ddsearch" placeholder="{{ trans('view.Button_Search') }}"/>
                    <draggable v-model="list.content" :group="{ name: 'g2' }" class="dropzone" :options="{draggable: '.dragdroplistitem', filter: 'input', preventOnFilter: false}">
                        <div class="dragdroplistitem" v-for="(item, id) in list.content" :key="item.id">
                            <div :class="item.id">@{{ item.id }} <i class="fa fa-cog dragdropitembutton" aria-hidden="true" v-on:click="itemmenu($event.target, key, id)"></i>
                            <div class="dragdropitemmenubox">
                              <span class="dragdropitemmenubutton" v-for="(listname, listkey) in lists" v-if="listkey != '0'" v-on:click="moveItem(key,listkey, id)">{{ trans('view.Button_DragDrop MoveTo') }} @{{ listname.name }}</span>
                              <span class="dragdropitemmenubutton" v-on:click="moveItem(key,-1, id)">{{ trans('view.Button_DragDrop MoveToNewList') }}</span>
                            </div>
                            <input type="text" name="oname" v-model="item.name"/>
                            </div>
                        </div>
                    </draggable>
                </div>
            </div>
        </div>
    </div>

</div>
@endif
@stop

@section('javascript_extra')
@if (multi_array_key_exists(['lists'], $additional_data))
<script>
Vue.directive('dispatchsel2', {
    inserted: function(app) {
        $(app).on('select2:select', function() {
            app.dispatchEvent(new Event('change'));
        });
        $(app).on('select2:unselect', function() {
            app.dispatchEvent(new Event('change'));
        });
    }
});
var app=new Vue({
    el: '#app',
    data: {
        listName: '',
        lists: @json($additional_data['lists'])
    },
    methods: {
        itemmenu: function(element, key, id) {
            targetElement = element.parentNode.getElementsByClassName("dragdropitemmenubox")[0];
            if (targetElement.style.display != "block"){
                targetElement.style.display = "block";
            }
            else {
                targetElement.style.display = "none";
            }
        },
        moveItem: function(olist, key, id) {
            // for creating a new list
            if (key == -1) {
                this.lists.push({
                    name: '{{ trans('view.Header_DragDrop Listname') }}',
                    content: []
                });
                key = this.lists.length-1;
            }
            // move item
            moveId = this.lists[olist].content[id].id;
            moveName = this.lists[olist].content[id].name;
            moveCalcOp = '';
            moveCalcVal = '';
            moveDiagramVar = '';
            moveDiagramOp = '';
            moveDiagramVal = '';
            this.lists[key].content.push({'id': moveId, 'name': moveName, 'calcOp': moveCalcOp, 'calcVal': moveCalcVal, 'diagramVar': moveDiagramVar, 'diagramOp': moveDiagramOp, 'diagramVal': moveDiagramVal});
            this.lists[olist].content.splice(id, 1);
        },
        addList: function() {
            if (! this.listName) {
                return;
            }

            this.lists.push({
                name: this.listName,
                content: []
            });

            this.listName = '';
        },
        delList: function(key) {
            if (key == 0) {
                // the list on the right side can not be deleted
                return;
            }

            // move elements from that list back to the main list
            for (var i=0;i < this.lists[key].content.length; i++) {
                moveId = this.lists[key].content[i].id;
                moveName = this.lists[key].content[i].name;
                // no calcOp/calcVal/diagramVar
                this.lists[0].content.push({'id': moveId, 'name': moveName});
            }

            // delete elements in reverse order so that the keys are not regenerated
            for (var i = this.lists[key].content.length-1; i >= 0; i--) {
                this.lists[key].content.splice(i, 1);
            }

            // delete the list
            this.lists.splice(key, 1);
        },
        ddFilter: function(key) {
            var search = $("#ddsearch")[0].value;
            if (search == "") {
                return null;
            }

            var refThis = this; // xhttps anonymous function overwrites the this-reference
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4) {
                    refThis.lists[0].content = JSON.parse(this.responseText);
                }
            };
            xhttp.open("GET", "{{route('Configfile.searchDeviceParams', $view_var->id )}}?search="+search, true);
            xhttp.send();
        }
    },
    updated: function () {
        this.$nextTick(function () {
            $("select").select2();

            json = {};
            params = {};
            for (var key = 1; key < this.lists.length; key++) {
                var listName = this.lists[key].name;
                for (var i = 0; i < this.lists[key].content.length; i++) {
                    let content = this.lists[key].content[i];
                    let calcOp = content.calcOp;
                    let calcVal = content.calcVal;
                    let diagramVar = content.diagramVar;
                    let diagramOp = content.diagramOp;
                    let diagramVal = content.diagramVal;

                    var calc = null;
                    if (calcOp !== null && calcVal !== null) {
                        calc = [calcOp, calcVal];
                    }

                    var diagram = null;
                    if (diagramVar !== null && diagramOp !== null && diagramVal !== null) {
                        diagram = [diagramVar, diagramOp, diagramVal];
                    }

                    params[content.name] = [content.id, calc, diagram];
                    json[listName] = params;
                }
            }

            $('input[name=monitoring]')[0].value = JSON.stringify(json);
        })
    },
});
</script>
@endif
@stop
