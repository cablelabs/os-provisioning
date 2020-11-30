@extends ('Generic.edit')

@section('content_left')
    @include ('Generic.logging')
    <?php
        $blade_type = 'relations';
    ?>

    @include('Generic.above_infos')
    {!! Form::model($view_var, ['route' => [$form_update, $view_var->id], 'method' => 'put', 'files' => true, 'id' => 'EditForm']) !!}

        @include($form_path, $view_var)

    {{ Form::close() }}

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
                            <input type="text" name="name" v-model="item.name"/>
                            <select name="operator" v-model="item.operator" v-dispatchsel2>
                                <option value=""></option>
                                <option value="+">+</option>
                                <option value="-">-</option>
                                <option value="*">*</option>
                                <option value="/">/</option>
                                <option value="%">%</option>
                            </select>
                            <input type="number" step="0.0001" name="opvalue" v-model="item.opvalue"/>
                            <select name="cvalue" v-model="item.cvalue" v-dispatchsel2>
                                <option value=""></option>
                                <option value="maxDsPow">maxDsPow</option>
                                <option value="avgUsSNR">avgUsSNR</option>
                                <option value="T4Timeout">T4Timeout</option>
                                <option value="maxUsPow">maxUsPow</option>
                                <option value="avgDsPow">avgDsPow</option>
                                <option value="T3Timeout">T3Timeout</option>
                                <option value="Uncorrectable">Uncorrectable</option>
                                <option value="avgUsPow">avgUsPow</option>
                                <option value="avgMuRef">avgMuRef</option>
                                <option value="minDsPow">minDsPow</option>
                                <option value="maxUsSNR">maxUsSNR</option>
                                <option value="minMuRef">minMuRef</option>
                                <option value="maxMuRef">maxMuRef</option>
                                <option value="minUsPow">minUsPow</option>
                                <option value="avgDsSNR">avgDsSNR</option>
                                <option value="minUsSNR">minUsSNR</option>
                                <option value="Corrected">Corrected</option>
                                <option value="maxDsSNR">maxDsSNR</option>
                                <option value="minDsSNR">minDsSNR</option>
                                <option value="ifHCInOctets">ifHCInOctets</option>
                                <option value="ifHCOutOctets">ifHCOutOctets</option>
                                <option value="avgDsAttn">avgDsAttn</option>
                                <option value="avgUsAttn">avgUsAttn</option>
                            </select>
                            <select name="coperator" v-model="item.coperator" v-dispatchsel2>
                                <option value=""></option>
                                <option value="+">+</option>
                                <option value="-">-</option>
                                <option value="*">*</option>
                                <option value="/">/</option>
                                <option value="%">%</option>
                            </select>
                            <input type="number" step="0.0001" name="copvalue" v-model="item.copvalue"/>
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
                            <input type="text" name="name" v-model="item.name" v-on:keyup="onKeyUp($event.target.value, key, id, $event.target.name)"/>
                            </div>
                        </div>
                    </draggable>
                </div>
            </div>
        </div>
    </div>

</div>
@stop

@section('javascript')
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
            moveOperator = '';
            moveOpValue = '';
            moveCValue = '';
            moveCOperator = '';
            moveCOpValue = '';
            this.lists[key].content.push({'id': moveId, 'name': moveName, 'operator': moveOperator, 'opvalue': moveOpValue, 'cvalue': moveCValue, 'coperator': moveCOperator, 'copvalue': moveCOpValue});
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
                // no operator/opvalue/cvalue
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
            json = '{';
            for (var key = 1; key<this.lists.length; key++) {
                if (key > 1) {
                    json += ',';
                }
                json += '"'+this.lists[key].name.replace('"','\\"') + '":{'

                injson = '';
                try {
                    for (var i = 0; i < this.lists[key].content.length; i++) {
                        insertId = this.lists[key].content[i].id.replace('"', '\\"');
                        insertName = this.lists[key].content[i].name.replace('"', '\\"');
                        insertOperator = null;
                        insertOpValue = null;
                        insertCValue = null;
                        insertCOperator = null;
                        insertCOpValue = null;
                        try {
                            if (this.lists[key].content[i].operator !== null && this.lists[key].content[i].operator !== '') {
                                insertOperator = this.lists[key].content[i].operator.replace('"', '\\"');
                            }
                        } catch (e) {}
                        try {
                            if (this.lists[key].content[i].opvalue !== null && this.lists[key].content[i].opvalue !== '') {
                                insertOpValue = this.lists[key].content[i].opvalue.toString().replace('"', '\\"');
                            }
                        } catch (e) {}
                        try {
                            if (this.lists[key].content[i].cvalue !== null && this.lists[key].content[i].cvalue !== '') {
                                insertCValue = '"' + this.lists[key].content[i].cvalue.toString().replace('"', '\\"') + '"';
                            }
                        } catch (e) {}
                        try {
                            if (this.lists[key].content[i].coperator !== null && this.lists[key].content[i].coperator !== '') {
                                insertCOperator = this.lists[key].content[i].coperator.toString().replace('"', '\\"');
                            }
                        } catch (e) {}
                        try {
                            if (this.lists[key].content[i].copvalue !== null && this.lists[key].content[i].copvalue !== '') {
                                insertCOpValue = this.lists[key].content[i].copvalue.toString().replace('"', '\\"');
                            }
                        } catch (e) {}

                        insertOps='null';
                        if (insertOperator!==null && insertOpValue!==null) {
                            insertOps='["' + insertOperator + '",' + insertOpValue + ']';
                        }

                        insertCOps='null';
                        if (insertCValue!==null && insertCOperator!==null && insertCOpValue!==null) {
                            insertCOps='[' + insertCValue + ',"' + insertCOperator + '",' + insertCOpValue + ']';
                        }

                        if (i>0) {
                            injson += ',';
                        }
                        injson += '"' + insertName + '":["' + insertId + '",' + insertOps + ',' + insertCOps + ']';
                    }
                }
                catch(err) {console.log(err);}
                injson += '}';

                json += injson;
            }
            json += '}';

            $('input[name=monitoring]')[0].value = json;
        })
    },
});
</script>
@endif
@stop
