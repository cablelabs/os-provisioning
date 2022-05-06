<div>
    <div class="mb-2">{{ $name }}:</div>
    <div>
        @php
            $$name = Modules\HfcReq\Entities\NetElement::where('netelementtype_id', $options['netTypeId'])->get();
        @endphp
        <div class="text-gray-900 flex items-center">
            <select2 class="select2-ajax"
                name="{{ $name }}" id="{{ $name }}"
                data-allow-clear="true"
                data-placeholder="Choose a {{ $name }}"
                ajax-route="{{ route('Sidebar.select2', ['nettype' => $options['netTypeId']] ) }}"
                @if (isset($ids) && $net = $ids->where('netelementtype.base_type_id', $options['netTypeId']))
                    initial-value="{{ $net->first()?->id }}"
                @endif
            >
                @if (isset($ids) && $net = $ids->where('netelementtype.base_type_id', $options['netTypeId']))
                    <option value="{{ $net->first()?->id }}" selected="selected">{{ $net->first()?->name }}</option>
                @endif
            </select2>
            <a href="{{ route($options['route'], ['netelement' => Modules\HfcReq\Entities\NetElement::where('netelementtype_id', $options['netTypeId'])->first()?->id ?? 643398])}}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-50 ml-1 hover:cursor-pointer hover:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>
