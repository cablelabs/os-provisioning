<div id="accordion2" class="mt-12 panel-group" v-if="capabilities">
    <div class="mb-0 bg-white border panel-inverse">
        <div class="flex flex-row items-center px-3 py-2 text-white bg-gray-900 rounded-top">
            <h3 class="flex-1 panel-title">
                <a class="accordion-toggle accordion-toggle-styled collapsed hover:text-gray-300" data-toggle="collapse"
                    data-parent="#accordion" href="#customCapabilities" aria-expanded="false">
                    <i class="mr-2 fa fa-plus-circle"></i>
                    {{ trans('view.Ability.Technical Capabilities') }}
                </a>
            </h3>
            <div class="flex items-center h-8 mx-1">
                <button class="btn btn-sm btn-primary" v-on:click="capabilityUpdate('all')"
                    v-show="loadingSpinner.capabilities">
                    <i class="fa fa-lg fa-circle-o-notch fa-spin mr-1"></i>
                    {{ trans('messages.Save') }}
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
                        </tr>
                    </thead>
                    <tr v-for="(capability, id) in capabilities">
                        <td v-text="capability.title"></td>
                        <td align="center">
                            <input type="checkbox" :ref="'capability' + id" :name="'capability[' + id + ']'"
                                value="maintain" :checked="capability.isCapable" v-on:change="capabilityUpdate(id)">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
