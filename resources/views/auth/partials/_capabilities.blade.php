<div id="accordion2" class="mt-12 panel-group" v-if="capabilities">
    <div class="mb-0 bg-white border panel-inverse">
        <div class="flex flex-row items-center px-3 py-2 text-white bg-zinc-900 rounded-top">
            <h3 class="flex-1 panel-title">
                <a class="accordion-toggle accordion-toggle-styled collapsed hover:text-gray-300" data-toggle="collapse"
                    data-parent="#accordion" href="#customCapabilities" aria-expanded="false">
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
                                value="maintain" :checked="capability.isCapable" v-on:change="capabilityChange(id)">
                        </td>
                        <td class="text-center">
                            <div
                                v-if="(capability.isCapable != originalCapabilities[id].isCapable) && showCapabilitySaveColumn">
                                <button type="submit" class="btn btn-primary" name="saveAbility" :value="id"
                                    v-on:click="capabilityUpdate(id)">
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
