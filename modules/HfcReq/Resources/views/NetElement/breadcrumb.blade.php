<div class="flex items-center">
    <div class="flex flex-col py-1 !px-3 text-slate-100 rounded bg-slate-800 hover:bg-slate-900">
        <a class="text-white hover:text:white no-underline" href="{{ route('NetElement.index') }}">
            <i class="fa fa-object-ungroup"></i>{{ trans_choice("view.Header_NetElement", 1) }}
        </a>
    </div>
</div>
@if ($column === 'all')
    <div class="flex items-center">
        <div class="flex flex-col px-2.5 text-black dark:text-slate-100">
            {{ trans("view.Menu_allNets") }}
        </div>
    </div>
@else
    {!! App\Http\Controllers\BaseViewController::compute_headline($route, $header, $rootNode) !!}
@endif
