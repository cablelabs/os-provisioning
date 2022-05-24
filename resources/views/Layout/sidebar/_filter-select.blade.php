<div>
    <div class="mb-2">{{ $name }}:</div>
    <div>
        @php
            $$name = Modules\HfcReq\Entities\NetElement::where('netelementtype_id', $options['netTypeId'])->get();
        @endphp
        <div class="flex items-center text-gray-900">
            <select2 id="{{ $name }}"
                class="select2-ajax"
                name="{{ $name }}"
                v-model="{{ $options['var'] }}"
                data-placeholder="Choose a {{ $name }}"
                data-allow-clear="true"
                :ajax-route="ajaxRoute('{{ route('Sidebar.select2', ['nettype' => $options['netTypeId']] ) }}')"
                @if (isset($ids) && $net = $ids->where('netelementtype.base_type_id', $options['netTypeId']))
                    :initial="{{ $net->first()?->id }}"
                @endif
            >
                @if (isset($ids) && $net = $ids->where('netelementtype.base_type_id', $options['netTypeId']))
                    <option value="{{ $net->first()?->id }}" selected="selected">{{ $net->first()?->name }}</option>
                @endif
            </select2>
            <a :href="route({{ $options['var'] }}, '{{ route($options['route'], ['netelement' => 'NETELEMENT_ID'])}}')">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 ml-1 text-gray-50 hover:cursor-pointer hover:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>
