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

@if (multi_array_key_exists(['lists', 'searchFlag'], $additional_data))
<script src="{{asset('components/assets-admin/plugins/vue/dist/vue.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/sortable/Sortable.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/vuedraggable/dist/vuedraggable.umd.min.js')}}"></script>

<div id="app" class="dragdropfield">
    <h2>Build your interface:</h2>
    <div class="box" id="left">
        <draggable v-model="lists" :group="{ name: 'g1' }" class="droplist" :options="{draggable: '.list-group', filter: 'input', preventOnFilter: false}">
            <div v-for="(list, key) in lists" v-if="key != '0'" class="list-group">
                <div class="listbox">
                    <div class="h">
                        <input type="text" v-model="list.name">
                        <button class="btn btn-primary" @click="delList(key)">Delete list</button>
                    </div>
                    <draggable v-model="list.content" :group="{ name: 'g2' }" class="dropzone" :options="{draggable: '.listitem', filter: 'input', preventOnFilter: false}">
                        <div class="listitem" v-for="(item, id) in list.content" :key="item.id">
                            <div :class="item.id">@{{ item.id }} <input type="text" name="name" :value="item.name" v-on:keyup="onKeyUp($event.target.value, key, id)"/></div>
                        </div>
                    </draggable>
                </div>
            </div>
        </draggable>

        <div class="newlist">
            <input type="text" v-model="listName" placeholder="Name of new list" />
            <button class="btn btn-primary" @click="addList()">Add new list</button>
        </div>
    </div>

    <div class="box" id="right">
        <div :group="{ name: 'g1' }" class="droplist" >
            <div v-for="(list, key) in lists" v-if="key == '0'" class="list-group">
                <div class="listbox">
                    <div class="h">
                        <input type="text" value="Parameters for this device" readonly="true">
                    </div>
                    <draggable v-model="list.content" :group="{ name: 'g2' }" class="dropzone" :options="{draggable: '.listitem', filter: 'input', preventOnFilter: false}">
                        <div class="listitem" v-for="(item, id) in list.content" :key="item.id">
                            <div :class="item.id">@{{ item.id }} <input type="text" name="name" :value="item.name" v-on:keyup="onKeyUp($event.target.value, key, id)"/></div>
                        </div>
                    </draggable>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
var app = new Vue({
    el: '#app',
    data: {
        listName: '',
        lists: @json($additional_data['lists'])
    },
    methods: {
        onKeyUp: function(newval, key, id) {
            this.lists[key].content[id].name=newval;
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
                //the list on the right side can not be deleted
                return;
            }

            //move elements from that list back to the main list
            for (var i=0;i<this.lists[key].content.length;i++) {
                moveId=this.lists[key].content[i].id;
                moveName=this.lists[key].content[i].name;
                this.lists[0].content.push({'id': moveId, 'name': moveName});
            }

            //delete elements in reverse order so that the keys are not regenerated
            for (var i=this.lists[key].content.length-1;i>=0;i--) {
                this.lists[key].content.splice(i, 1);
            }

            //delete the list
            this.lists.splice(key, 1);
        },
    },
    updated: function () {
        this.$nextTick(function () {
            json='{';
            for (var key=1;key<this.lists.length;key++) {
                if (key>1) {
                    json+=',';
                }
                json+='"'+this.lists[key].name.replace('"','\\"')+'":{'

                injson='';
                try {
                    for (var i=0;i<this.lists[key].content.length;i++) {
                        insertId=this.lists[key].content[i].id.replace('"','\\"');
                        insertName=this.lists[key].content[i].name.replace('"','\\"');
                        if (i>0) {
                            injson+=',';
                        }
                        injson+='"'+insertName+'":"'+insertId+'"';
                    }
                }
                catch(err) {}
                injson+='}';

                json+=injson;
            }
            json+='}';

            json='{{$additional_data['searchFlag']}}'+json;

            //appending/replacing
            if (document.getElementById("text").value.toLowerCase().indexOf("{{$additional_data['searchFlag']}}") === -1) {
                document.getElementById("text").value=json+"\n\n"+document.getElementById("text").value;
            }
            else {
                document.getElementById("text").value=document.getElementById("text").value.replace(/^{{$additional_data['searchFlag']}}(.*)$/mg,json);
            }
        })
    },
});
</script>
@endif

@stop
