<div class="col-item">
    <div class="mb-2">{{ $name }}:</div>
    <div>
        @php
            $$name = Modules\HfcReq\Entities\NetElement::where('netelementtype_id', $options['netTypeId'])->get();
        @endphp
        <div class="flex items-center text-gray-900 [&>span.select2]:!w-[92%]">
            @php
                $net = isset($ids) ? $ids->where('netelementtype.base_type_id', $options['netTypeId'])->first() : null;
            @endphp
            <sidebar-select2 id="{{ $name }}"
                class="select2-ajax"
                name="{{ $name }}"
                v-model="{{ $options['var'] }}"
                data-model="{{ $options['var'] }}"
                @updateref="updateref"
                data-placeholder="Choose a {{ $name }}"
                data-allow-clear="true"
                :ajax-route="ajaxRoute('{{ route('Sidebar.select2', ['nettype' => $options['netTypeId']] ) }}')"
                @if ($net)
                    :initial="{{ $net->id ?? 0 }}"
                    data-net="{{ $net->id }}"
                @elseif (isset($cpe) && $name == 'CPE')
                    :initial="{{ $cpe->id }}"
                @endif
            >
                @if ($net)
                    <option value="{{ $net->id }}" selected="selected">{{ $net->name }}</option>
                @endif
                @if (isset($cpe) && $name == 'CPE')
                    <option value="{{ $cpe->id }}" selected="selected">{{ $cpe->mac }}</option>
                @endif
            </sidebar-select2>
            <a :href="route({{ $options['var'] }}, '{{ route($options['route'], ['netelement' => 'NETELEMENT_ID'])}}')" class="w-[8%]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 ml-1 text-gray-50 hover:cursor-pointer hover:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>
