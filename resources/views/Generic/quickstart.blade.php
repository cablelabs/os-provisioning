<style>
    a:hover {
        text-decoration: none;
    }
</style>

<div class="widget widget-stats bg-grey">
    {{-- info/data --}}
    <div class="stats-info text-center">
        @foreach($view_header_links as $module_name => $typearray)
            @if (isset($typearray['submenu']))
                @if($module_name == $route_name)
                    @foreach ($typearray['submenu'] as $type => $valuearray)
                        {!! HTML::decode (HTML::linkRoute(str_replace("index","create",$valuearray['link']),
                            '<span class="btn btn-dark p-10 m-5 m-r-10 text-center">
                                <i style="font-size: 25px;" class="img-center fa '.$valuearray['icon'].' p-10"></i><br />
                                <span class="username text-ellipsis text-center">'.trans_choice('view.Button_Create '.str_replace(".index","",$valuearray['link']), 1).'</span>
                            </span>'))
                        !!}
                    @endforeach
                @endif
            @endif
        @endforeach
    </div>
    {{-- reference link --}}
    <div class="stats-link"><a href="#">{{ trans('view.Dashboard_Quickstart') }}</a></div>
</div>
