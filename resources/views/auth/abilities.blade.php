@DivOpen(12)
<div id="failure" class="alert alert-danger" hidden></div>
<div class="d-flex justify-content-center">
    <div id="loader"></div>
</div>
<div id="app" style="display:none;">
    <div id="accordion" class="panel-group">
        <div class="panel panel-inverse">
            <div class="panel-heading d-flex align-items-center flex-row">
                <h3 class="panel-title" style="flex: 1;">
                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion" href="#customAbilities" aria-expanded="false">
                        <i class="fa fa-plus-circle"></i>
                        {{ trans('view.Ability.Custom Abilities') }}
                    </a>
                </h3>
                <div class="d-flex align-items-center mx-1">
                    <button class="btn btn-sm btn-primary"
                        v-on:click="customUpdate('all')"
                        v-show="showSaveColumn">
                        <i class="fa fa-lg"
                           :class="loadingSpinner.custom ?'fa-circle-o-notch fa-spin' : 'fa-save'">
                        </i>
                        {{ trans('messages.Save All') }}
                    </button>
                </div>
            </div>
            <div id="customAbilities" class="panel-collapse collapse" aria-expanded="true" style="">
                <div class="panel-body d-flex flex-column">
                    <table class="table table-hover mb-5">
                        <thead class="text-center">
                          <tr>
                            <th class="text-left">{{ trans('messages.Ability') }}</th>
                            <th>{{ trans('messages.Allow') }}</th>
                            <th v-if="allowAll">{{ trans('messages.Forbid') }}</th>
                            <th v-show="!showSaveColumn"></th>
                            <th v-show="showSaveColumn">{{ trans('messages.Save Changes') }}</th>
                            <th>{{ trans('messages.Help') }}</th>
                          </tr>
                        </thead>
                        <tr v-for="(ability, id) in customAbilities">
                            <td v-text="ability['localTitle']"></td>
                            <td align="center">
                                <input type="checkbox"
                                    :ref="'allowed' + id"
                                    :name="'ability[' + id + ']'"
                                    value="allow"
                                    v-show="id == allowAllId || (!allowAll && id != allowAllId) || allowAll == undefined"
                                    v-on:change="customAllow(id)">
                            </td>
                            <td align="center" v-show="allowAll">
                                <input type="checkbox"
                                    :ref="'forbidden' + id"
                                    :name="'ability[' + id + ']'"
                                    value="forbid"
                                    v-show="checkForbiddenVisibility(id)"
                                    v-on:change="customForbid(id)">
                            </td>
                            <td class="text-center">
                                <div v-if="changed[id] && showSaveColumn">
                                    <button type="submit"
                                        class="btn btn-primary"
                                        name="saveAbility"
                                        :value="id"
                                        v-on:click="customUpdate(id)">
                                        <i class="fa fa-save fa-lg"
                                           :class="loadingSpinner.custom ?'fa-circle-o-notch fa-spin' : 'fa-save'">
                                        </i>
                                        {{ trans('messages.Save') }}
                                    </button>
                                </div>
                            </td>
                            <td align="center">
                                <a data-toggle="popover"
                                    data-container="body"
                                    data-trigger="hover"
                                    data-placement="right"
                                    :title="ability['localTitle']"
                                    :data-content="ability['helperText']">
                                <i class="fa fa-2x text-info p-t-5 fa-question-circle"></i>
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    @foreach ($modelAbilities as $module => $entities)
        <form method="POST" action="{!! route('modelAbility.update') !!}" accept-charset="UTF-8" id="{{ $module }}" class="form_open" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="panel panel-inverse">
            <div class="panel-heading d-flex align-items-center flex-row">
                <h3 class="panel-title" style="flex: 1;">
                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion" href="#{{ 'group_'.$module }}" aria-expanded="false">
                        <i class="fa fa-plus-circle"></i>
                        {{ trans("view.Ability.{$module}") }}
                    </a>
                </h3>
                <span class="d-flex align-items-center">
                        <span
                            class="badge badge-lg mr-1"
                            :class="[allowAll ? 'badge-danger' : 'badge-success']" >
                            @{{ allowAll ? button.forbid : button.allow }}
                        </span>
                    @foreach($actions as $action)
                        @if($action['name'] != 'delete' && $action['name'] != 'create' || $module != 'GlobalConfig')
                        <span class="">
                            <button class="btn btn-sm d-none d-md-block mx-1"
                                    name="{!! $action['name'] . '_' . $module !!}"
                                    v-on:click.prevent="shortcutButtonClick"
                                    :class="[{!! $action['name'] !!}All.{!! $module !!} ? 'btn-{!! $action['bsclass'] !!} {!! $action['name'] != 'update' ? 'active' : ''!!}' : 'btn-secondary']"
                                    @if(in_array($action['name'], ['create', 'update', 'delete']))
                                    v-if="!(manageAll.{!! $module !!})"
                                    @endif
                                    @if($action['name'] == 'view')
                                    v-if="!(manageAll.{!! $module !!}) && allowViewAll == undefined"
                                    @endif
                                    title="{{ App\Http\Controllers\BaseViewController::translate_view($action['name'], 'Button' )}}"
                                    >
                                <span class="d-block d-xl-none" style="pointer-events: none;">
                                <i class="fa {!! $action['icon'] !!} fa-lg"
                                    :class="[{!! $action['name'] !!}All.{!! $module !!} ? '' : 'text-dark']"></i>
                                </span>
                                <span class="d-none d-xl-block" style="pointer-events: none;">
                                    <i class="fa {!! $action['icon'] !!} fa-lg"
                                        :class="[{!! $action['name'] !!}All.{!! $module !!} ? '' : 'text-dark']"></i>
                                    {{ trans('messages.'.Str::title($action['name'])) }}
                                </span>
                            </button>
                        </span>
                        @endif
                    @endforeach
                    <span class="d-flex ml-1">
                        <button class="btn btn-sm btn-primary"
                            name="{!! 'save' . '_' . $module !!}"
                            v-if="saveButton('{!! $module !!}')"
                            v-on:click.prevent="modelUpdate('{!! $module !!}')">
                            <span class="d-block d-xl-none">
                                <i class="fa fa-lg"
                                   :class="loadingSpinner.{!! $module !!} ?'fa-circle-o-notch fa-spin' : 'fa-save'">
                                </i>
                            </span>
                            <span class="d-none d-xl-block">
                                <i class="fa fa-lg"
                                   :class="loadingSpinner.{!! $module !!} ?'fa-circle-o-notch fa-spin' : 'fa-save'">
                                </i>
                                {{ trans('messages.Save') }}
                            </span>
                        </button>
                    </span>
                </span>
            </div>
            <div id="{{'group_'.$module}}" class="panel-collapse collapse" aria-expanded="true" style="">
                <div class="panel-body d-flex flex-column">
                <table class="table table-hover">
                    <thead class="text-center">
                      <tr>
                        <th class="text-left"> {{ trans('messages.Name') }} </th>
                        <th > {{ trans('messages.Allow'). '-'.
                                 trans('messages.Forbid') }} </th>
                        <th > {{ trans('messages.Manage') }} </th>
                        <th v-if="!(manageAll.{!! $module !!}) && allowViewAll == undefined">
                            {{ trans('messages.View') }}
                        </th>
                        @if ($module != 'GlobalConfig')
                        <th v-if="!(manageAll.{!! $module !!})">
                            {{ trans('messages.Create') }}
                        </th>
                        @endif
                        <th v-if="!(manageAll.{!! $module !!})">
                            {{ trans('messages.Update') }}
                        </th>
                        @if ($module != 'GlobalConfig')
                        <th v-if="!(manageAll.{!! $module !!})">
                            {{ trans('messages.Delete') }}
                        </th>
                        @endif
                      </tr>
                    </thead>
                @foreach ($entities as $name => $permission)
                    <tr>
                        <td width="44%">
                            {{ trans('messages.'.$name) }}
                        </td>
                        <td width="8%" align="center">
                            <span class="badge" :class="[allowAll ? 'badge-danger' : 'badge-success']" >
                                @{{ allowAll ? button.forbid : button.allow }}
                            </span>
                        </td>
                        @foreach ($actions as $actionValue => $action)
                            @if($action['name'] != 'delete' && $action['name'] != 'create' || $module != 'GlobalConfig')
                            <td width="8%"
                                align="center"
                                @if(in_array($action['name'], ['create', 'update', 'delete']))
                                v-if="!(manageAll.{!! $module !!})"
                                @endif
                                @if($action['name'] == 'view')
                                v-if="!(manageAll.{!! $module !!}) && allowViewAll == undefined"
                                @endif
                                >
                                <input name="{{ $name }}[]"
                                        value="{!! $actionValue !!}"
                                        id="{!! $action['name'] . '_' . $module . '_' . $name !!}"
                                        ref="{!! $action['name'] . '_' . $module . '_' . $name !!}"
                                        v-model="{!! 'modelAbilities' . '.' . $module . '.' . $name !!}"
                                        v-on:change="changeModelAbility"
                                        @if(in_array($action['name'], ['view', 'create', 'update', 'delete']))
                                        v-show="showInput('{!! 'manage'. '_' . $module . '_' . $name !!}')"
                                        @endif
                                        type="checkbox"
                                        align="left">
                            </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
    </div>
    </form>
    @endforeach
    </div>
    <div id="accordion2" class="panel-group" style="margin-top: 40px">
        <div class="panel panel-inverse">
            <div class="panel-heading d-flex align-items-center flex-row">
                <h3 class="panel-title" style="flex: 1;">
                    <a class="accordion-toggle accordion-toggle-styled collapsed"
                        data-toggle="collapse"
                        data-parent="#accordion"
                        href="#customCapabilities"
                        aria-expanded="false">
                        <i class="fa fa-plus-circle"></i>
                        {{ trans('view.Ability.Technical Capabilities') }}
                    </a>
                </h3>
                <div class="d-flex align-items-center mx-1">
                    <button class="btn btn-sm btn-primary"
                        v-on:click="capabilityUpdate('all')"
                        v-show="showCapabilitySaveColumn">
                        <i class="fa fa-lg"
                            :class="loadingSpinner.custom ?'fa-circle-o-notch fa-spin' : 'fa-save'">
                        </i>
                        {{ trans('messages.Save All') }}
                    </button>
                </div>
            </div>
            <div id="customCapabilities" class="panel-collapse collapse" aria-expanded="true" style="">
                <div class="panel-body d-flex flex-column">
                    <table class="table table-hover mb-5">
                        <thead class="text-center">
                            <tr>
                            <th class="text-left">{{ trans('view.Ability.Capability') }}</th>
                            <th>{{ trans('view.Ability.Can Maintain') }}</th>
                            <th v-show="!showCapabilitySaveColumn"></th>
                            <th v-show="showCapabilitySaveColumn">{{ trans('messages.Save Changes') }}</th>
                            </tr>
                        </thead>
                        <tr v-for="(capability, id) in capabilities">
                            <td v-text="capability.title"></td>
                            <td align="center">
                                <input type="checkbox"
                                :ref="'capability' + id"
                                :name="'capability[' + id + ']'"
                                value="maintain"
                                :checked="capability.isCapable"
                                v-on:change="capabilityChange(id)">
                            </td>
                            <td class="text-center">
                                <div v-if="(capability.isCapable != originalCapabilities[id].isCapable) && showCapabilitySaveColumn">
                                    <button type="submit"
                                        class="btn btn-primary"
                                        name="saveAbility"
                                        :value="id"
                                        v-on:click="capabilityUpdate(id)">
                                        <i class="fa fa-save fa-lg"
                                            :class="loadingSpinner.capabilities ?'fa-circle-o-notch fa-spin' : 'fa-save'">
                                        </i>
                                        {{ trans('messages.Save') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@DivClose()

@section('javascript_extra')
<script src="{{asset('components/assets-admin/plugins/Abilities/es6-promise.auto.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/vue/dist/vue.min.js')}}"></script>
{{-- When in Development use this Version
    <script src="{{asset('components/assets-admin/plugins/vue/dist/vue.js')}}"></script>
--}}
<script src="{{asset('components/assets-admin/plugins/Abilities/lodash.core.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/Abilities/axios.min.js')}}"></script>
<script type="text/javascript">
function handlePanelPositionToPreventCrash() {
    return new Promise(function(resolve, reject) {
        let targetPage = window.location.href;
            targetPage = targetPage.split('?');
            targetPage = targetPage[0];
        let panelPositionData = localStorage.getItem(targetPage) ? targetPage : "{!! isset($view_header) ? $view_header : 'undefined'!!}";
        if (panelPositionData) {
            localStorage.removeItem(panelPositionData)
            document.getElementById("loader").style.display = "none";
            document.getElementById("app").style.display = "block";
            resolve();
        } else {
            document.getElementById("loader").style.display = "none";
            document.getElementById("app").style.display = "block";
            resolve();
        }
    });
}

handlePanelPositionToPreventCrash().then(function() {
new Vue({
  el: '#app',
  data() {
    return {
        allowAll: undefined,
        allowAllId: 1,
        allowViewAll: undefined,
        allowViewAllId: 2,
        manageAll: {},
        viewAll: {},
        createAll: {},
        updateAll: {},
        deleteAll: {},
        saveAll: {},
        loadingSpinner: {},
        spinner: false,
        changed: [],
        showSaveColumn: false,
        showCapabilitySaveColumn: false,
        capabilities: @json($capabilities),
        originalCapabilities: @json($capabilities),
        customAbilities: @json($customAbilities),
        roleAbilities: @json($roleAbilities),
        originalRoleAbilities: @json($roleAbilities),
        roleForbiddenAbilities: @json($roleForbiddenAbilities),
        originalForbiddenAbilities: @json($roleForbiddenAbilities),
        modelAbilities: @json($modelAbilities),
        originalModelAbilities: @json($modelAbilities),
        button: {
            allow: '{{ trans("messages.Allow to") }}',
            forbid: '{{ trans("messages.Forbid to") }}'
            }
    }
  },
  mounted() {
      this.setupCustomAbilities();
      this.setupModelAbilities();
  },
  methods: {
    setupCustomAbilities : function () {
        for (id in this.customAbilities) {
            if (this.customAbilities[id]['title'] == 'All abilities')
                this.allowAllId = id;

            if (this.customAbilities[id]['title'] == 'View everything')
                this.allowViewAllId = id;
        }

        for (id in this.customAbilities) {
            if (id in this.originalRoleAbilities) {
                this.$refs['allowed' + id][0].checked = true;
                if (id == this.allowAllId)  this.allowAll = true;
                if (id == this.allowViewAllId) this.allowViewAll = true;
            }

            if (id in this.originalForbiddenAbilities) {
                this.$refs['forbidden' + id][0].checked = true;
                if (id == this.allowAllId) this.allowAll = false;
                if (id == this.allowViewAllId) this.allowViewAll = false;
            }

            this.changed[id] = false;
        }

        this.loadingSpinner.custom = false;
    },
    setupModelAbilities : function() {
        for (let module in this.modelAbilities) {
            this.manageAll[module] = this.checkShortcutButtons('*', module);
            this.viewAll[module] = this.checkShortcutButtons('view', module);
            this.createAll[module] = this.checkShortcutButtons('create', module);
            this.updateAll[module] = this.checkShortcutButtons('update', module);
            this.deleteAll[module] = this.checkShortcutButtons('delete', module);
            this.saveAll[module] = this.checkShortcutButtons('save', module);
            this.loadingSpinner[module] = false;
        }
    },
    checkForbiddenVisibility : function (id) {
        if (id == this.allowViewAllId || id == this.allowAllId )
            return false;

        return ((this.allowAll && id != this.allowAllId) || (this.allowAll == undefined));
    },
    checkChangedArray : function (array) {
        return array.includes(true) ? true : false;
    },
    hasChanged : function (id) {
        if (this.$refs['allowed' + id][0].checked)
            return id in this.originalRoleAbilities ? false : true;

        if (this.$refs['forbidden' + id][0].checked)
            return id in this.originalForbiddenAbilities ? false  : true;

        if (!this.$refs['allowed' + id][0].checked || !this.$refs['forbidden' + id][0].checked)
            return id in this.originalRoleAbilities || id in this.originalForbiddenAbilities ? true : false;
    },
    customAllow : function (id) {
        if (this.$refs['allowed' + id][0].checked) {
            if (id == this.allowAllId) {
                this.allowAll = true;
                this.allowViewAll = undefined;
                this.$refs['allowed' + this.allowViewAllId][0].checked = false;
                this.changed.splice(this.allowViewAllId, 1, this.hasChanged(this.allowViewAllId));
                delete this.roleAbilities[this.allowViewAllId];
            }

            this.allowViewAll =  id == this.allowViewAllId ? true : this.allowViewAll;
            this.roleAbilities[id] = this.customAbilities[id]['localTitle'];
            delete this.roleForbiddenAbilities[id];
        } else {
            if (id == this.allowAllId) {
                this.allowAll = undefined;
                this.changed.splice(this.allowViewAllId, 1, this.hasChanged(this.allowViewAllId));
            }

            this.allowViewAll =  id == this.allowViewAllId ? undefined : this.allowViewAll;
            delete this.roleAbilities[id];
        }

        this.$refs['forbidden' + id][0].checked = false;
        this.changed.splice(id, 1, this.hasChanged(id));
        this.showSaveColumn = this.checkChangedArray(this.changed);
    },
    customForbid : function (id) {
        if (this.$refs['forbidden' + id][0].checked) {
            this.roleForbiddenAbilities[id] = this.customAbilities[id]['localTitle'];
            delete this.roleAbilities[id];
        } else {
            delete this.roleForbiddenAbilities[id];
        }

        this.$refs['allowed' + id][0].checked = false;
        this.changed.splice(id, 1, this.hasChanged(id));
        this.showSaveColumn = this.checkChangedArray(this.changed);
    },
    changeModelAbility : function (event) {
        let name = (event.target.id).split('_');
        let action = name[0];
        let actionShortcut = (action == 'manage') ? '*' : action;
        let module = name[1];

        if (!event.target.checked)
            this.$data[action + 'All'][module] = false;
        else {
            this.$data[action + 'All'][module] = this.checkShortcutButtons(actionShortcut, module);
        }
    },
    showInput : function(elementId) {
        return !document.getElementById(elementId).checked;
    },
    saveButton: function (module) {
        if (_.isEqual(this.modelAbilities[module], this.originalModelAbilities[module]))
            return false;

        return true;
    },
    shortcutButtonClick : function (event) {
        let module = event.target.name.split('_');
        this.setShortcutButtons(module[0], module[1]);
    },
    setShortcutButtons : function (action, module) {
        let actionShortcut = (action == 'manage') ? '*' : action;

        if (!this.$data[action + 'All'][module]) {
            this.$data[action + 'All'][module] = true;
            for (let model in this.modelAbilities[module]) {
                let set = new Set(this.modelAbilities[module][model]);
                set.add(actionShortcut);
                this.modelAbilities[module][model] = Array.from(set);
            }
        } else {
            this.$data[action + 'All'][module] = false;
            for (let model in this.modelAbilities[module]) {
                if (actionShortcut == '*') document.getElementById('manage' + '_' + module + '_'  + model).checked = false;
                let set = new Set(this.modelAbilities[module][model]);
                set.delete(actionShortcut);
                this.modelAbilities[module][model] = Array.from(set);
            }
        }
    },
    checkShortcutButtons : function(actionShortcut, module) {
            let check = true

            for (let model in this.modelAbilities[module]) {
                let set = new Set(this.modelAbilities[module][model]);
                check = check && set.has(actionShortcut);
                this.modelAbilities[module][model] = Array.from(set);
            }

            return check;
    },
    capabilityChange: function (id) {
        this.capabilities[id].isCapable = !this.capabilities[id].isCapable
        this.showCapabilitySaveColumn = this.checkChangedArray(
            Object.keys(this.capabilities).map(key => this.capabilities[key].isCapable));
    },
    capabilityUpdate: function (id) {
        let self = this;
        let token = document.querySelector('input[name="_token"]').value;

        this.loadingSpinner.capabilities = true;
        this.loadingSpinner = _.clone(this.loadingSpinner);

        axios({
            method: 'post',
            url: '{!! route("capability.update") !!}',
            headers: {'X-CSRF-TOKEN': token},
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            data: {
                id: id,
                capabilities: this.capabilities,
                roleId: '{{ $view_var->id }}'
            }
        })
        .then(function (response) {
            self.originalCapabilities = response.data.capabilities

            self.loadingSpinner.capabilities = false;
            self.showCapabilitySaveColumn = self.checkChangedArray(self.changed);
        })
        .catch(function (error) {
            alert(error);
        });
    },
    customUpdate : function (id) {
        let self = this;
        let token = document.querySelector('input[name="_token"]').value;

        this.loadingSpinner.custom = true;
        this.loadingSpinner = _.clone(this.loadingSpinner);

        axios({
            method: 'post',
            url: '{!! route("customAbility.update") !!}',
            headers: {'X-CSRF-TOKEN': token},
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            data: {
                id: id,
                roleAbilities: this.roleAbilities,
                roleForbiddenAbilities: this.roleForbiddenAbilities,
                changed: this.changed,
                roleId: '{{ $view_var->id }}'
            }
        })
        .then(function (response) {
            self.originalRoleAbilities = response.data.roleAbilities;
            self.originalForbiddenAbilities = response.data.roleForbiddenAbilities;

            if(self.changed[self.allowAllId]) {
                for (module in self.modelAbilities) {
                    self.modelUpdate(module);
                }
            }

            if (typeof response.data.id === 'object'){
                for (let id in response.data.id) {
                    self.changed.splice(response.data.id[id], 1, self.hasChanged(response.data.id[id]));
                }
            } else {
                self.changed.splice(response.data.id, 1, self.hasChanged(response.data.id));
            }

            self.loadingSpinner.custom = false;
            self.showSaveColumn = self.checkChangedArray(self.changed);
        })
        .catch(function (error) {
            alert(error);
        });
    },
    modelUpdate : function(module) {
        this.loadingSpinner[module] = true;
        this.loadingSpinner = _.clone(this.loadingSpinner); // let watcher know something changed
        let self = this;
        let form = document.getElementById(module);
        let formData = new FormData(form);

        formData.append('roleId', {{ $view_var->id }});
        formData.append('allowAll', this.allowAll);
        formData.append('module', module);

        axios({
            method: 'post',
            url: '{!! route("modelAbility.update") !!}',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            data: formData,
        })
        .then(function (response) {
            self.originalModelAbilities = response.data;
            self.modelAbilities[module] = _.clone(response.data[module]);
            self.loadingSpinner[module] = false;
        })
        .catch(function (error) {
            alert(error);
        });
    }
  }
});
$('[data-toggle="popover"]').popover({html:true});
});
</script>
@stop
