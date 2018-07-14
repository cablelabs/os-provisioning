@DivOpen(12)

<div id="failure" class="alert alert-danger" hidden></div>
<div id="app">
    <div id="accordion" class="panel-group">
        {!! Form::open(array('route' => array('customAbility.update', null), 'method' => 'POST')) !!}
        @section ('customAbilities')
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
                        value="isAllowed"
                        v-show="id == 1 || (!allowAll && id != 1) || allowAll == undefined"
                        v-on:change="customAllow(id)">
                </td>
                <td align="center">
                    <input type="checkbox"
                        :ref="'forbidden' + id"
                        :name="'ability[' + id + ']'"
                        value="isForbidden"
                        v-show="id == 1 || (allowAll && id != 1) || allowAll == undefined"
                        v-on:change="customForbid(id)">
                </td>
                <td class="text-center">
                    <div v-if="changed[id] && showSaveColumn">
                        <button type="submit"
                            class="btn btn-success"
                            :name="saveAbility"
                            :value="id"
                            v-on:click="customUpdate(id)">
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
        @stop
        {!! Form::close() !!}

        @include('bootstrap.group', ['header' => 'Custom Abilities', 'content' => 'customAbilities'])

        @foreach ($modelAbilities as $module => $entities)
            @section ('group_'.$module)
                <table class="table table-hover">
                    <thead class="text-center">
                        <th> {{ App\Http\Controllers\BaseViewController::translate_label('Name') }} </th>
                        <th > {{ App\Http\Controllers\BaseViewController::translate_label('Allow'). '/'.
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
                @foreach ($entities as $name)
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
                            <input name="{{ $name }}"
                                    value="*"
                                    type="checkbox"
                                    align="left">
                        </td>
                        <td width="8%" align="center">
                            <input name="{{ $name }}"
                                    value="View"
                                    type="checkbox"
                                    align="left">
                        </td>
                        @if ($module != 'GlobalConfig')
                        <td width="8%" align="center">
                            <input name="{{ $name }}"
                                    value="Create"
                                    type="checkbox"
                                    align="left">
                        </td>
                        @endif
                        <td width="8%" align="center">
                            <input name="{{ $name }}"
                                    value="Update"
                                    type="checkbox"
                                    align="left">
                        </td>
                        @if ($module != 'GlobalConfig')
                        <td width="8%" align="center">
                            <input name="{{ $name }}"
                                    value="Delete"
                                    type="checkbox"
                                    align="left">
                        </td>
                        @endif
                    </tr>
                @endforeach
                </table>
            @stop
        @include('bootstrap.group', ['header' => $module, 'content' => 'group_'.$module])
        @endforeach
    {!! Form::close() !!}
    </div>
</div>
@DivClose()

<script src="https://unpkg.com/vue@2.5.16/dist/vue.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.core.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script> --}}
<script type="text/javascript">

new Vue({
  el: '#app',
  data() {
    return {
        customAbilities: {!! $customAbilities->toJson() !!},
        modelAbilities: {!! $modelAbilities->toJson() !!},
        originalRoleAbilities: {!! $roleAbilities->toJson() !!},
        roleAbilities: {!! $roleAbilities->toJson() !!},
        originalForbiddenAbilities: {!! $roleForbiddenAbilities->toJson() !!},
        roleForbiddenAbilities: {!! $roleForbiddenAbilities->toJson() !!},
        allowAll: undefined,
        changed: [],
        showSaveColumn: false,
        button: {
            allow: '{!! App\Http\Controllers\BaseViewController::translate_label("Allow to") !!}',
            forbid: '{!! App\Http\Controllers\BaseViewController::translate_label("Forbid to") !!}'
            },
        helperTitle: {},
        helperText: {}
    }
  },
  mounted() {
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
  methods: {
    customAllow(id) {
        if (this.$refs['allowed'+ id][0].checked) {
            if (id == 1) this.allowAll = true;
            this.roleAbilities.custom[id] = this.customAbilities[id]['title'];
            delete this.roleForbiddenAbilities.custom[id];
        } else {
            this.allowAll = undefined;
            delete this.roleAbilities.custom[id];
        }

        this.$refs['forbidden'+ id][0].checked = false;
        this.changed.splice(id, 1, this.hasChanged(id));
        this.showSaveColumn = this.checkChanged();
    },
    customForbid(id) {
        if (this.$refs['forbidden'+ id][0].checked) {
            if (id == 1) this.allowAll = false;
            this.roleForbiddenAbilities.custom[id] = this.customAbilities[id]['title'];
            delete this.roleAbilities.custom[id];
        } else {
            this.allowAll = undefined;
            delete this.roleForbiddenAbilities.custom[id];
        }

        this.$refs['allowed'+ id][0].checked = false;
        this.changed.splice(id, 1, this.hasChanged(id));
        this.showSaveColumn = this.checkChanged();
    },
    hasChanged(id) {
        if (this.$refs['allowed' + id][0].checked)
            return id in this.originalRoleAbilities.custom ? false : true;

        if (this.$refs['forbidden' + id][0].checked)
            return id in this.originalForbiddenAbilities.custom ? false  : true;

        if (!this.$refs['allowed' + id][0].checked)
            return id in this.originalRoleAbilities.custom || id in this.originalForbiddenAbilities.custom ? true : false;

        if (!this.$refs['forbidden' + id][0].checked)
            return id in this.originalForbiddenAbilities.custom || id in this.originalRoleAbilities.custom ? true  : false;
    },
    checkChanged() {
        return this.changed.includes(true) ? true : false;
    },
    customUpdate(id) {
        let token = document.querySelector('input[name="_token"]').value;

        axios({
            method: 'post',
            url: '{!! route('customAbility.update') !!}',
            headers: {'X-CSRF-TOKEN': token},
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            data: {
                _token: token,
                id: id,
                allowAll: this.allowAll,
                roleAbilities: this.roleAbilities.custom,
                roleForbiddenAbilities: this.roleForbiddenAbilities.custom,

            }
        })
        .then(function (response) {
            console.log(response);
        })
        .catch(function (error) {
            console.log(error);
        });
    }
  }
});

{{--
function updateRoleAbility(Ability)
{
    var msg = '';
    var delay = 1000;
    var data = Ability.val().split('_');
    var url = window.location.protocol + '//' + window.location.host + '/nmsprime/admin/Authrole/UpdateAbility';
    var token = document.querySelector('input[name="_token"]').value;

    $.ajaxSetup({
        url: url,
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        success: function (data) {
            if (data == 1) {
                msg = 'Ability "' + authmetacore_right + '" for ID ' + authmethacore_id + ' updated successfully.';
            } else {
                msg = 'Error while updating user Ability (ID: ' + authmethacore_id + ', Ability: ' + authmetacore_right + '). ' + data;
                $("#failure").text(msg).attr('hidden', false);
                delay = 3000;
            }
            console.log(msg);

            // reload the page
            window.setTimeout(function() {
                location.reload();
            }, delay);
        }
    });

    $.ajax({
        data: {
            authmethacore_id: authmethacore_id,
            authmethacore_right: authmetacore_right,
            authmethacore_right_value: authmetacore_right_value
        },
    compare() {
        if (this.allowAll) {
           let equal = _.isEqual(this.roleAbilities, this.originalRoleAbilities);
            return !equal;
        }

        if (!this.allowAll) {
            let equal = _.isEqual(this.roleForbiddenAbilities, this.originalForbiddenAbilities);
            return !equal;
        }
    }
    });
}
--}}
</script>
