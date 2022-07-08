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

<div id="failure" class="alert alert-danger" hidden></div>
<div class="flex justify-center">
    <div id="loader"></div>
</div>
<div id="auth-abilities" v-pre v-cloak data-view-var-id="{{ $view_var->id }}"
    data-custom-abilities='@json($customAbilities, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT)' data-role-abilities='@json($roleAbilities, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT)'
    data-role-forbidden-abilities='@json($roleForbiddenAbilities, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT)'
    data-ability-allow-to="{{ trans('view.Ability.Allow to') }}"
    data-ability-forbid-to="{{ trans('view.Ability.Forbid to') }}"
    data-route-custom-ability-update="{!! route('customAbility.update') !!}" data-view-header="{!! isset($view_header) ? $view_header : 'undefined' !!}"
    data-capabilities='@json($capabilities, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT)' data-model-abilities='@json($modelAbilities, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT)'
    data-route-capability-update="{!! route('capability.update') !!}" data-route-model-ability-update="{!! route('modelAbility.update') !!}">
    <div id="accordion" class="panel-group">
        <div class="mb-0 bg-white border panel-inverse">
            <div class="flex flex-row items-center px-3 py-2 text-white bg-zinc-900 rounded-top">
                <h3 class="flex-1 panel-title">
                    <a class="accordion-toggle accordion-toggle-styled collapsed hover:text-gray-300"
                        data-toggle="collapse" data-parent="#accordion" href="#customAbilities" aria-expanded="false">
                        <i class="mr-2 fa fa-plus-circle"></i>
                        {{ trans('view.Ability.Custom Abilities') }}
                    </a>
                </h3>
                <div class="flex items-center h-8 mx-1">
                    <button class="btn btn-sm btn-primary" v-on:click="customUpdate('all')" v-show="showSaveColumn">
                        <i class="fa fa-lg" :class="loadingSpinner.custom ? 'fa-circle-o-notch fa-spin' : 'fa-save'">
                        </i>
                        {{ trans('view.Ability.Save All') }}
                    </button>
                </div>
            </div>
            <div id="customAbilities" class="panel-collapse collapse" aria-expanded="true">
                <div class="flex panel-body flex-column">
                    <table class="table mb-5 table-hover">
                        <thead class="text-center">
                            <tr>
                                <th class="text-left">{{ trans('view.Ability.Ability') }}</th>
                                <th>{{ trans('view.Ability.Allow') }}</th>
                                <th v-if="allowAll">{{ trans('view.Ability.Forbid') }}</th>
                                <th v-show="!showSaveColumn"></th>
                                <th v-show="showSaveColumn">{{ trans('view.Ability.Save Changes') }}</th>
                                <th>{{ trans('view.Ability.Help') }}</th>
                            </tr>
                        </thead>
                        <tr v-for="(ability, id) in customAbilities">
                            <td v-text="ability['localTitle']"></td>
                            <td align="center">
                                <input type="checkbox" :id="'allowed' + id" :name="'ability[' + id + ']'"
                                    value="allow"
                                    v-show="id == allowAllId || (!allowAll && id != allowAllId) || allowAll == undefined"
                                    v-on:change="customAllow(id)">
                            </td>
                            <td align="center" v-show="allowAll">
                                <input type="checkbox" :id="'forbidden' + id" :name="'ability[' + id + ']'"
                                    value="forbid" v-show="checkForbiddenVisibility(id)" v-on:change="customForbid(id)">
                            </td>
                            <td class="text-center">
                                <div v-if="changed[id] && showSaveColumn">
                                    <button type="submit" class="btn btn-primary" name="saveAbility"
                                        :value="id" v-on:click="customUpdate(id)">
                                        <i class="fa fa-save fa-lg"
                                            :class="loadingSpinner.custom ? 'fa-circle-o-notch fa-spin' : 'fa-save'">
                                        </i>
                                        {{ trans('messages.Save') }}
                                    </button>
                                </div>
                            </td>
                            <td align="center">
                                <a class="cursor-pointer" data-toggle="popover" data-container="body"
                                    data-trigger="hover" data-placement="right" :title="ability['localTitle']"
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
            <form method="POST" action="{!! route('modelAbility.update') !!}" accept-charset="UTF-8" id="{{ $module }}"
                class="form_open" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="mb-0 bg-white border panel-inverse">
                    <div class="flex flex-row items-center px-3 py-2 text-white bg-zinc-900">
                        <h3 class="flex-1 panel-title">
                            <a class="accordion-toggle accordion-toggle-styled collapsed hover:text-gray-300"
                                data-toggle="collapse" data-parent="#accordion" href="#{{ 'group_' . $module }}"
                                aria-expanded="false">
                                <i class="mr-2 fa fa-plus-circle"></i>
                                {{ trans("view.Ability.{$module}") }}
                            </a>
                        </h3>
                        <span class="flex items-center">
                            <span class="mr-1 badge badge-lg" :class="[allowAll ? 'badge-danger' : 'badge-success']"
                                v-text="allowAll ? button.forbid : button.allow">
                            </span>
                            @foreach ($actions as $action)
                                @if (($action['name'] != 'delete' && $action['name'] != 'create') || $module != 'GlobalConfig')
                                    <span class="h-8">
                                        <button class="mx-1 btn btn-sm d-none d-md-block" name="{!! $action['name'] . '_' . $module !!}"
                                            v-on:click.prevent="shortcutButtonClick"
                                            :class="[permissions.{!! $action['name'] !!}.{!! $module !!} ?
                                                'btn-{!! $action['bsclass'] !!} {!! $action['name'] != 'update' ? 'active' : '' !!}' : 'btn-secondary'
                                            ]"
                                            @if (in_array($action['name'], ['create', 'update', 'delete'])) v-if="!(permissions.manage.{!! $module !!})"
                                    @elseif ($action['name'] == 'view')
                                    v-if="!(permissions.manage.{!! $module !!}) && allowViewAll == undefined" @endif
                                            title="{{ trans('view.Button_' . $action['name']) }}">
                                            <span class="d-block d-wide-none" style="pointer-events: none;">
                                                <i class="fa {!! $action['icon'] !!} fa-lg"
                                                    :class="[permissions.{!! $action['name'] !!}.{!! $module !!} ?
                                                        '' : 'text-dark'
                                                    ]"></i>
                                            </span>
                                            <span class="d-none d-wide-block" style="pointer-events: none;">
                                                <i class="fa {!! $action['icon'] !!} fa-lg"
                                                    :class="[permissions.{!! $action['name'] !!}.{!! $module !!} ?
                                                        '' : 'text-dark'
                                                    ]"></i>
                                                {{ trans('view.Ability.' . ucfirst($action['name'])) }}
                                            </span>
                                        </button>
                                    </span>
                                @endif
                            @endforeach
                            <span class="flex ml-1">
                                <button class="btn btn-sm btn-primary" name="{!! 'save' . '_' . $module !!}"
                                    v-if="saveButton('{!! $module !!}')"
                                    v-on:click.prevent="modelUpdate('{!! $module !!}')">
                                    <span class="d-block d-xl-none">
                                        <i class="fa fa-lg"
                                            :class="loadingSpinner.{!! $module !!} ? 'fa-circle-o-notch fa-spin' :
                                                'fa-save'">
                                        </i>
                                    </span>
                                    <span class="d-none d-xl-block">
                                        <i class="fa fa-lg"
                                            :class="loadingSpinner.{!! $module !!} ? 'fa-circle-o-notch fa-spin' :
                                                'fa-save'">
                                        </i>
                                        {{ trans('messages.Save') }}
                                    </span>
                                </button>
                            </span>
                        </span>
                    </div>
                    <div id="{{ 'group_' . $module }}" class="panel-collapse collapse" aria-expanded="true">
                        <div class="flex panel-body flex-column">
                            <table class="table table-hover">
                                <thead class="text-center">
                                    <tr>
                                        <th class="text-left"> {{ trans('view.name') }} </th>
                                        <th> {{ trans('view.Ability.Allow') . '-' . trans('view.Ability.Forbid') }}
                                        </th>
                                        <th> {{ trans('view.Ability.Manage') }} </th>
                                        <th
                                            v-if="!(permissions.manage.{!! $module !!}) && allowViewAll == undefined">
                                            {{ trans('view.Ability.View') }}
                                        </th>
                                        @if ($module != 'GlobalConfig')
                                            <th v-if="!(permissions.manage.{!! $module !!})">
                                                {{ trans('view.Ability.Create') }}
                                            </th>
                                        @endif
                                        <th v-if="!(permissions.manage.{!! $module !!})">
                                            {{ trans('view.Ability.Update') }}
                                        </th>
                                        @if ($module != 'GlobalConfig')
                                            <th v-if="!(permissions.manage.{!! $module !!})">
                                                {{ trans('view.Ability.Delete') }}
                                            </th>
                                        @endif
                                    </tr>
                                </thead>
                                @foreach ($entities as $name => $permission)
                                    <tr>
                                        <td width="44%">
                                            @if (in_array($module, ['GlobalConfig', 'Authentication', 'Ccc']))
                                                {{ trans('messages.' . $name) }}
                                            @else
                                                {{ trans_choice('view.Header_' . $name, 2) }}
                                            @endif
                                        </td>
                                        <td width="8%" align="center">
                                            <span class="badge"
                                                :class="[allowAll ? 'badge-danger' : 'badge-success']">
                                                @{{ allowAll ? button.forbid : button.allow }}
                                            </span>
                                        </td>
                                        @foreach ($actions as $actionValue => $action)
                                            @if (($action['name'] != 'delete' && $action['name'] != 'create') || $module != 'GlobalConfig')
                                                <td width="8%" align="center"
                                                    @if (in_array($action['name'], ['create', 'update', 'delete'])) v-if="!(permissions.manage.{!! $module !!})" @endif
                                                    @if ($action['name'] == 'view') v-if="!(permissions.manage.{!! $module !!}) && allowViewAll == undefined" @endif>
                                                    <input name="{{ $name }}[]"
                                                        value="{!! $actionValue !!}" id="{!! $action['name'] . '_' . $module . '_' . $name !!}"
                                                        ref="{!! $action['name'] . '_' . $module . '_' . $name !!}"
                                                        v-model="{!! 'modelAbilities' . '.' . $module . '.' . $name !!}"
                                                        v-on:change="changeModelAbility"
                                                        @if (in_array($action['name'], ['view', 'create', 'update', 'delete'])) v-show="showInput('{!! 'manage' . '_' . $module . '_' . $name !!}')" @endif
                                                        type="checkbox" align="left">
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
    <div id="accordion2" class="mt-12 panel-group" v-if="capabilities">
        <div class="mb-0 bg-white border panel-inverse">
            <div class="flex flex-row items-center px-3 py-2 text-white bg-zinc-900 rounded-top">
                <h3 class="flex-1 panel-title">
                    <a class="accordion-toggle accordion-toggle-styled collapsed hover:text-gray-300"
                        data-toggle="collapse" data-parent="#accordion" href="#customCapabilities"
                        aria-expanded="false">
                        <i class="mr-2 fa fa-plus-circle"></i>
                        {{ trans('view.Ability.Technical Capabilities') }}
                    </a>
                </h3>
                <div class="flex items-center h-8 mx-1">
                    <button class="btn btn-sm btn-primary" v-on:click="capabilityUpdate('all')"
                        v-show="showCapabilitySaveColumn">
                        <i class="fa fa-lg" :class="loadingSpinner.custom ? 'fa-circle-o-notch fa-spin' : 'fa-save'">
                        </i>
                        {{ trans('view.Ability.Save All') }}
                    </button>
                </div>
            </div>
            <div id="customCapabilities" class="panel-collapse collapse" aria-expanded="true">
                <div class="flex panel-body flex-column">
                    <table class="table mb-5 table-hover">
                        <thead class="text-center">
                            <tr>
                                <th class="text-left">{{ trans('view.Ability.Capability') }}</th>
                                <th>{{ trans('view.Ability.Can Maintain') }}</th>
                                <th>
                                    <div v-show="showCapabilitySaveColumn">{{ trans('messages.Save Changes') }}
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tr v-for="(capability, id) in capabilities">
                            <td v-text="capability.title"></td>
                            <td align="center">
                                <input type="checkbox" :ref="'capability' + id" :name="'capability[' + id + ']'"
                                    value="maintain" :checked="capability.isCapable"
                                    v-on:change="capabilityChange(id)">
                            </td>
                            <td class="text-center">
                                <div
                                    v-if="(capability.isCapable != originalCapabilities[id].isCapable) && showCapabilitySaveColumn">
                                    <button type="submit" class="btn btn-primary" name="saveAbility"
                                        :value="id" v-on:click="capabilityUpdate(id)">
                                        <i class="fa fa-save fa-lg"
                                            :class="loadingSpinner.capabilities ? 'fa-circle-o-notch fa-spin' : 'fa-save'">
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
