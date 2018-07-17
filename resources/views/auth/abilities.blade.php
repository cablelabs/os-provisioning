@DivOpen(12)
<div id="failure" class="alert alert-danger" hidden></div>
<div id="app">
    <div id="accordion" class="panel-group">
        <div class="panel panel-inverse">
            <div class="panel-heading d-flex align-items-baseline">
                <h3 class="panel-title" style="flex: 1;">
                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion" href="#customAbilities" aria-expanded="false">
                        <i class="fa fa-plus-circle"></i>
                        {{ App\Http\Controllers\BaseViewController::translate_view('Custom Abilities', 'Ability') }}
                    </a>
                </h3>
                <span class="d-flex ml-1">
                    <button class="btn btn-sm btn-primary"
                        v-on:click="customUpdate('all')"
                        v-show="showSaveColumn">
                        <i class="fa fa-save fa-lg"></i>
                        {{ trans('messages.Save All') }}
                    </button>
                </span>
            </div>
            <div id="customAbilities" class="panel-collapse collapse" aria-expanded="true" style="">
                <div class="panel-body d-flex flex-column">
                    <table class="table table-hover mb-5">
                        <thead class="text-center">
                            <th>{{ App\Http\Controllers\BaseViewController::translate_label('Ability') }}</th>
                            <th>{{ App\Http\Controllers\BaseViewController::translate_label('Allow') }}</th>
                            <th>{{ App\Http\Controllers\BaseViewController::translate_label('Forbid') }}</th>
                            <th v-show="!showSaveColumn"></th>
                            <th v-show="showSaveColumn">{{ App\Http\Controllers\BaseViewController::translate_label('Save Changes') }}</th>
                            <th>{{ App\Http\Controllers\BaseViewController::translate_label('Help') }}</th>
                        </thead>
                        <tr v-for="(ability, id) in customAbilities">
                            <td v-text="ability['title']"></td>
                            <td align="center">
                                <input type="checkbox"
                                    :ref="'allowed' + id"
                                    :name="'ability[' + id + ']'"
                                    value="allow"
                                    v-show="id == 1 || (!allowAll && id != 1) || allowAll == undefined"
                                    v-on:change="customAllow(id)">
                            </td>
                            <td align="center">
                                <input type="checkbox"
                                    :ref="'forbidden' + id"
                                    :name="'ability[' + id + ']'"
                                    value="forbid"
                                    v-show="id == 1 || (allowAll && id != 1) || allowAll == undefined"
                                    v-on:change="customForbid(id)">
                            </td>
                            <td class="text-center">
                                <div v-if="changed[id] && showSaveColumn">
                                    <button type="submit"
                                        class="btn btn-primary"
                                        name="saveAbility"
                                        :value="id"
                                        v-on:click="customUpdate(id)">
                                        <i class="fa fa-save fa-lg"></i>
                                        {{ trans('messages.Save') }}
                                    </button>
                                </div>
                            </td>
                            <td align="center">
                                <a data-toggle="popover"
                                    data-container="body"
                                    data-trigger="hover"
                                    data-placement="right"
                                    :data-content="ability['helperText']"
                                    :title="ability['title']">
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
            <div class="panel-heading d-flex align-items-baseline">
                <h3 class="panel-title" style="flex: 1;">
                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion" href="#{{ 'group_'.$module }}" aria-expanded="false">
                        <i class="fa fa-plus-circle"></i>
                        {{ App\Http\Controllers\BaseViewController::translate_view($module, 'Ability') }}
                    </a>
                </h3>
                @foreach(['fa-star' => 'manage','fa-eye' => 'view', 'fa-plus' => 'create', 'fa-pencil' => 'update', 'fa-trash' => 'delete'] as $icon => $permission)
                @if($permission != 'delete' && $permission != 'create' || $module != 'GlobalConfig')
                <span class="d-flex mr-1">
                    <button class="btn btn-sm d-none d-md-block"
                            :class="[{!! $permission !!}All.{!! $module !!} ? 'btn-success active' : 'btn-secondary']"
                            v-on:click.prevent="shortcutButton"
                            @if(in_array($permission, ['view', 'create', 'update', 'delete']))
                            v-if="!(manageAll.{!! $module !!})"
                            @endif
                            name="{!! $permission . '_' . $module !!}">
                        <span class="d-block d-xl-none">
                        <i class="fa {!! $icon !!}"
                            :class="[{!! $permission !!}All.{!! $module !!} ? 'text-warning' : '']"></i>
                        </span>
                        <span class="d-none d-xl-block">
                            <i class="fa {!! $icon !!}"
                                :class="[{!! $permission !!}All.{!! $module !!} ? 'text-warning' : '']"></i>
                            {{ App\Http\Controllers\BaseViewController::translate_label(Str::title($permission)) }}
                        </span>
                    </button>
                </span>
                @endif
                @endforeach
                <span class="d-flex ml-1">
                    <button class="btn btn-sm btn-primary" type="submit" name="{!! 'save' . '_' . $module !!}">
                        <span class="d-block d-xl-none">
                            <i class="fa fa-save fa-xl"></i>
                        </span>
                        <span class="d-none d-xl-block">
                            <i class="fa fa-save"></i>
                            {{ trans('messages.Save') }}
                        </span>
                    </button>
                </span>
            </div>
            <div id="{{'group_'.$module}}" class="panel-collapse collapse" aria-expanded="true" style="">
                <div class="panel-body d-flex flex-column">
                <table class="table table-hover">
                    <thead class="text-center">
                        <th> {{ App\Http\Controllers\BaseViewController::translate_label('Name') }} </th>
                        <th > {{ App\Http\Controllers\BaseViewController::translate_label('Allow'). '-'.
                                 App\Http\Controllers\BaseViewController::translate_label('Forbid') }} </th>
                        <th > {{ App\Http\Controllers\BaseViewController::translate_label('Manage') }} </th>
                        <th v-if=""> {{ App\Http\Controllers\BaseViewController::translate_label('View') }} </th>
                        @if ($module != 'GlobalConfig')
                        <th> {{ App\Http\Controllers\BaseViewController::translate_label('Create') }} </th>
                        @endif
                        <th> {{ App\Http\Controllers\BaseViewController::translate_label('Update') }} </th>
                        @if ($module != 'GlobalConfig')
                        <th> {{ App\Http\Controllers\BaseViewController::translate_label('Delete') }} </th>
                        @endif
                    </thead>
                @foreach ($entities as $name => $permission)
                    <tr>
                        <td width="44%">
                            {{ App\Http\Controllers\BaseViewController::translate_label($name) }}
                        </td>
                        <td width="8%" align="center">
                            <span class="badge" :class="[allowAll ? 'badge-danger' : 'badge-success']" >
                                @{{ allowAll ? button.forbid : button.allow }}
                            </span>
                        </td>
                        <td width="8%" align="center">
                            <input name="{{ $name }}[]"
                                    value="*"
                                    id="{!! 'manage' . $module . $name !!}"
                                    v-model="{!! 'modelAbilities' . '.' . $module . '.' . $name !!}"
                                    type="checkbox"
                                    align="left">
                        </td>
                        <td width="8%" align="center">
                            <input name="{{ $name }}[]"
                                    value="view"
                                    id="{!! 'view' . $module . $name !!}"
                                    v-model="{!! 'modelAbilities' . '.' . $module . '.' . $name !!}"
                                    type="checkbox"
                                    align="left">
                        </td>
                        @if ($module != 'GlobalConfig')
                        <td width="8%" align="center">
                            <input name="{{ $name }}[]"
                                    value="create"
                                    id="{!! 'create' . $module . $name !!}"
                                    v-model="{!! 'modelAbilities' . '.' . $module . '.' . $name !!}"
                                    type="checkbox"
                                    align="left">
                        </td>
                        @endif
                        <td width="8%" align="center">
                            <input name="{{ $name }}[]"
                                    value="update"
                                    id="{!! 'update' . $module . $name !!}"
                                    v-model="{!! 'modelAbilities' . '.' . $module . '.' . $name !!}"
                                    type="checkbox"
                                    align="left">
                        </td>
                        @if ($module != 'GlobalConfig')
                        <td width="8%" align="center">
                            <input name="{{ $name }}[]"
                                    value="delete"
                                    id="{!! 'delete' . $module . $name !!}"
                                    v-model="{!! 'modelAbilities' . '.' . $module . '.' . $name !!}"
                                    type="checkbox"
                                    align="left">
                        </td>
                        @endif
                    </tr>
                @endforeach
                </table>
			</div>
		</div>
	</div>
    </form>
    @endforeach
    </div>
</div>
@DivClose()

@section('javascript_extra')
<script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script>
<script src="https://unpkg.com/vue@2.5.16/dist/vue.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.core.min.js"></script>
<script type="text/javascript">
function waitForPanelPositionToLoadToPreventCrash() {
    return new Promise(function(resolve, reject) {
        let targetPage = window.location.href;
            targetPage = targetPage.split('?');
            targetPage = targetPage[0];
        let panelPositionData = localStorage.getItem(targetPage);
        if (panelPositionData) {
            $(window).on('localstorage-position-loaded', function() {
                resolve();
            });
        } else {
            resolve();
        }
    });
}

waitForPanelPositionToLoadToPreventCrash().then(function() {
new Vue({
  el: '#app',
  data() {
    return {
        allowAll: undefined,
        manageAll: {},
        viewAll: {},
        createAll: {},
        updateAll: {},
        deleteAll: {},
        changed: [],
        showSaveColumn: false,
        customAbilities: {!! $customAbilities->toJson() !!},
        roleAbilities: {!! $roleAbilities->toJson() !!},
        originalRoleAbilities: {!! $roleAbilities->toJson() !!},
        roleForbiddenAbilities: {!! $roleForbiddenAbilities->toJson() !!},
        originalForbiddenAbilities: {!! $roleForbiddenAbilities->toJson() !!},
        modelAbilities: {!! $modelAbilities->toJson() !!},
        originalModelAbilities: {!! $modelAbilities->toJson() !!},
        button: {
            allow: '{!! App\Http\Controllers\BaseViewController::translate_label("Allow to") !!}',
            forbid: '{!! App\Http\Controllers\BaseViewController::translate_label("Forbid to") !!}'
            }
    }
  },
  mounted() {
      this.setupCustomAbilities();
      this.setupModelAbilities();
  },
  methods: {
    setupCustomAbilities : function () {
        if (1 in this.roleAbilities.custom)
            this.allowAll = true;

        if (1 in this.roleForbiddenAbilities.custom)
            this.allowAll = false;

        for (id in this.customAbilities) {
            if (id in this.originalRoleAbilities.custom)
                this.$refs['allowed'+ id][0].checked = true;

            if (id in this.originalForbiddenAbilities.custom)
                this.$refs['forbidden'+ id][0].checked = true;

            this.changed[id] = false;
        }
    },
    setupModelAbilities :function() {
        for (let module in this.modelAbilities) {
            this.manageAll[module] = false;
            this.viewAll[module] = false;
            this.createAll[module] = false;
            this.updateAll[module] = false;
            this.deleteAll[module] = false;
        }
    },
    customAllow : function (id) {
        if (this.$refs['allowed'+ id][0].checked) {
            if (id == 1) this.allowAll = true;
            this.roleAbilities.custom[id] = this.customAbilities[id]['title'];
            delete this.roleForbiddenAbilities.custom[id];
        } else {
            if (id == 1) this.allowAll = undefined;
            delete this.roleAbilities.custom[id];
        }

        this.$refs['forbidden'+ id][0].checked = false;
        this.changed.splice(id, 1, this.hasChanged(id));
        this.showSaveColumn = this.checkChanged(this.changed);
    },
    customForbid: function (id) {
        if (this.$refs['forbidden'+ id][0].checked) {
            if (id == 1) this.allowAll = false;
            this.roleForbiddenAbilities.custom[id] = this.customAbilities[id]['title'];
            delete this.roleAbilities.custom[id];
        } else {
            if (id == 1) this.allowAll = undefined;
            delete this.roleForbiddenAbilities.custom[id];
        }

        this.$refs['allowed'+ id][0].checked = false;
        this.changed.splice(id, 1, this.hasChanged(id));
        this.showSaveColumn = this.checkChanged(this.changed);
    },
    customUpdate : function (id) {
        let self = this;
        let token = document.querySelector('input[name="_token"]').value;

        axios({
            method: 'post',
            url: '{!! route("customAbility.update") !!}',
            headers: {'X-CSRF-TOKEN': token},
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            data: {
                id: id,
                roleAbilities: this.roleAbilities.custom,
                roleForbiddenAbilities: this.roleForbiddenAbilities.custom,
                changed: this.changed,
                roleId: '{!! $view_var->id !!}'
            }
        })
        .then(function (response) {
            self.originalRoleAbilities.custom = response.data.roleAbilities.custom;
            self.originalForbiddenAbilities.custom = response.data.roleForbiddenAbilities.custom;
            for (let id in response.data.id) {
                self.changed.splice(response.data.id[id], 1, self.hasChanged(response.data.id[id]));
            }
            self.showSaveColumn = self.checkChanged(self.changed);
        })
        .catch(function (error) {
            alert(error);
        });
    },
    checkChanged : function (array) {
        return array.includes(true) ? true : false;
    },
    hasChanged : function (id) {
        if (this.$refs['allowed' + id][0].checked)
            return id in this.originalRoleAbilities.custom ? false : true;

        if (this.$refs['forbidden' + id][0].checked)
            return id in this.originalForbiddenAbilities.custom ? false  : true;

        if (!this.$refs['allowed' + id][0].checked)
            return id in this.originalRoleAbilities.custom || id in this.originalForbiddenAbilities.custom ? true : false;

        if (!this.$refs['forbidden' + id][0].checked)
            return id in this.originalForbiddenAbilities.custom || id in this.originalRoleAbilities.custom ? true  : false;
    },
    shortcutButton : function (event) {
        let module = event.target.name.split('_');

        if (!this.$data[module[0] + 'All'][module[1]]) {
            this.$data[module[0] + 'All'][module[1]] = true;
            if (module[0] == 'manage') module[0] = '*';

            for (let model in this.modelAbilities[module[1]]) {
                let set = new Set(this.modelAbilities[module[1]][model]);
                set.add(module[0]);
                this.modelAbilities[module[1]][model] = Array.from(set);
            }
        } else {
            this.$data[module[0] + 'All'][module[1]] = false;
            if (module[0] == 'manage') module[0] = '*';

            for (let model in this.modelAbilities[module[1]]) {
                let set = new Set(this.modelAbilities[module[1]][model]);
                set.delete(module[0]);
                this.modelAbilities[module[1]][model] = Array.from(set);
            }
        }
    }
  }
});
});
</script>
@stop
