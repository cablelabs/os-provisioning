{{-- begin #sidebar --}}
<div id="sidebar" class="sidebar d-print-none">
  {{-- begin sidebar scrollbar --}}
  <div data-scrollbar="true" data-height="100%">
    {{-- begin sidebar user --}}
    <ul class="nav">
      <li class="nav-profile">
        <div class="info">
          {!! $framework['header2'] !!}
          <small>Version {!! $framework['version'] !!}</small>
        </div>
      </li>
    </ul>
    {{-- end sidebar user --}}

    {{-- begin sidebar nav --}}
    <ul class="nav">
      <li class="nav-header">
        <a href="{{route('Apps.active')}}">Apps</a>
      </li>
      @foreach ($view_header_links as $module_name => $typearray)
        <li id="{{ Str::slug($module_name,'_')}}" class="has-sub {{ ($route_name == $module_name) ? 'active' : ''}}" data-sidebar="level1">
          <div style="padding: 8px 20px;line-height: 20px;color: #a8acb1;display: flex;justify-content: space-between;align-items: center;" class="recolor">
            @if (isset($typearray['link']))
              <a href="{{route($typearray['link'])}}">
            @else
              <a class="caret-link" href="javascript:;">
            @endif
            @if (is_file(public_path('images/apps/').$typearray['icon']))
              <img src="{{ asset('images/apps/'.$typearray['icon']) }}" style="height: 20px; margin-right: 7px; filter: saturate(25%) brightness(80%);">
            @else
              <i class="fa fa-fw {{ $typearray['icon'] }}"></i>
            @endif
            <span>{{$typearray['translated_name'] ?? $module_name}}</span>
            </a>
            @if(isset($typearray['submenu']))
              <a class="caret-link" href="javascript:;" style="width: 20%; height: 20px; display:block; text-align: right">
                <b class="caret"></b>
              </a>
            @endif
          </div>
        @if (isset($typearray['submenu']))
          <ul class="sub-menu line">
          @foreach ($typearray['submenu'] as $type => $valuearray)
          <li id="menu-{{ Str::slug($type,'_') }}">
            <a href="{{ route($valuearray['link']) }}" style="overflow: hidden; white-space: nowrap;">
              <i class="fa fa-fw {{ $valuearray['icon'] }}"></i>
              <span>{{ $type }}</span>
            </a>
          </li>
          @endforeach
          </ul>
        @endif
        </li>
      @endforeach

    @can('view', Modules\HfcBase\Entities\TreeErd::class)
      <li class="nav-header">{{ trans('view.Menu_Nets') }}</li>
      <li id="network_overview" class="has-sub" data-sidebar="level1">
        <div style="display: flex;justify-content:space-between;padding: 8px 20px;line-height: 20px;">
          @if (Module::collections()->has('HfcBase'))
            <a href="{{ route('TreeErd.show', ['field' => 'all', 'search' => 1]) }}">
              <i class="fa fa-sitemap"></i>
              <span>{{ trans('view.Menu_allNets') }}</span>
            </a>
            <a class="caret-link" href="javascript:;">
              <b class="caret"></b>
            </a>
          @else
            <a class="caret-link" style="flex:1;display: flex;justify-content:space-between;align-items:center;" href="javascript:;">
              <div>
                <i class="fa fa-sitemap"></i>
                <span>{{ trans('view.Menu_allNets') }}</span>
              </div>
              <b class="caret"></b>
            </a>
          @endif
        </div>
        <ul class="sub-menu" style="display: none;padding-left:21px;">
          @foreach ($networks as $network)
            <li id="network_{{$network->id}}" class="has-sub" data-sidebar="level2">
              <div style="display: flex;justify-content:space-between;padding: 0.25rem 1.25rem 0.25rem 0;">
                <a href="{{ route('TreeErd.show', ['field' => 'net', 'search' => $network->id]) }}" style="color: #889097;">
                  <i class="fa fa-circle text-success"></i>
                  <span>{{$network->name}}</span>
                </a>
                @if($network->clusters->isNotEmpty())
                  <a class="caret-link" style="color: #889097;" href="javascript:;">
                    <b class="caret"></b>
                  </a>
                @endif
              </div>
              <ul class="sub-menu line sub-line" style="display: none;padding: 0;">
                {{-- Network-Clusters are Cached for 5 minutes --}}
                @foreach ($network->clusters as $cluster)
                  <li id="cluster_{{$cluster->id}}">
                    <a href="{{ route('TreeErd.show', ['field' => 'cluster', 'search' => $cluster->id]) }}" style="width: 100%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;">
                      <i class="fa fa-circle-thin text-info"></i>
                      {{$cluster->name}}
                    </a>
                  </li>
                @endforeach
              </ul>
            </li>
          @endforeach
        </ul>
      </li>
    @endcan
    {{-- sidebar minify button --}}
    <li>
      <a href="javascript:;" class="sidebar-minify-btn hidden-xs" data-click="sidebar-minify">
        <i class="fa fa-angle-double-left"></i>
      </a>
    </li>
  </ul>
  {{-- end sidebar nav --}}
  </div>
{{-- end sidebar scrollbar --}}
</div>
{{-- end #sidebar --}}
<div class="sidebar-bg d-print-none"></div>


{{-- java script dynamic panel on right top side under tabs --}}
@if(isset($panel_right_extra))
   {{-- begin theme-panel --}}
    <div class="theme-panel">
      <a href="javascript:;" data-click="theme-panel-expand" class="theme-collapse-btn">
        <i class="fa fa-cog"></i>
      </a>
      <div class="theme-panel-content">
        <h5 class="m-t-0">Menu</h5>

        <h4>
          @foreach ($panel_right_extra as $menu)
            <?php
              $route = $menu['route'];
              $name  = $menu['name'];
              $link  = $menu['link'];
            ?>
            <br> {{ HTML::linkRoute($route, $name, $link) }}
          @endforeach
        </h4>

      </div>
    </div>
   {{-- end theme-panel --}}
@endif
