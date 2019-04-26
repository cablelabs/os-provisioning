{{-- begin #sidebar --}}
<div id="sidebar" class="sidebar">
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
      @if (\Module::collections()->has('Dashboard'))
        <li id ="dashboardsidebar" class="{{ ($route_name == 'Dashboard') ? 'active' : ''}}">
          <a href="{{route('Dashboard.index')}}">
          <i class="fa fa-home"></i>
          <span>Dashboard</span></a>
        </li>
      @endif

      <li class="nav-header">Navigation</li>
      @foreach ($view_header_links as $module_name => $typearray)
        @if (isset($typearray['submenu']))
        <li id="{{ Str::slug($module_name,'_')}}" class="has-sub {{ ($route_name == $module_name) ? 'active' : ''}}" data-sidebar="level1">
            <div style="padding: 8px 20px;line-height: 20px;color: #a8acb1;display: flex;justify-content: space-between;align-items: center;" class="">
              @if (isset($typearray['link']))
                <a href="{{route($typearray['link'])}}">
                  <i class="fa fa-fw {{ $typearray['icon'] }}"></i>
                  <span>{{$typearray['translated_name'] ?? $module_name}}</span>
                </a>
              @else
                <a class="caret-link" href="javascript:;">
                  <i class="fa fa-fw {{ $typearray['icon'] }}"></i>
                  <span>{{$typearray['translated_name'] ?? $module_name}}</span>
                </a>
              @endif
              <a class="caret-link" href="javascript:;" style="width: 20%; height: 20px; display:block; text-align: right">
                <b class="caret"></b>
              </a>
            </div>
          <ul class="sub-menu">
          @foreach ($typearray['submenu'] as $type => $valuearray)
          <li id="{{  Str::slug($type,'_') }}">
            <a href="{{route($valuearray['link'])}}" style="overflow: hidden; white-space: nowrap;">
              <i class="fa fa-fw {{ $valuearray['icon'] }}"></i>
              <span>{{ $type }}</span>
            </a>
          </li>
          @endforeach
          </ul>
        </li>
        @endif
      @endforeach

    @can('view', Modules\HfcBase\Entities\TreeErd::class)

      <li class="nav-header">Networks</li>
      @foreach ($networks as $network)
        <li id="network_{{$network->id}}" class="has-sub" data-sidebar="level1">
          <a href="javascript:;">
            <i class="fa fa-sitemap"></i>
            <b class="caret pull-right"></b>
            <span>{{$network->name}}</span>
          </a>
          <ul class="sub-menu" style="padding-left:0;list-style:none;">
            <li id="{{$network->name}}" class="has-sub" data-sidebar="level2">
              <a href="{{BaseRoute::get_base_url()}}/Tree/erd/net/{{$network->id}}">
                <i class="fa fa-circle text-success"></i>
                {{$network->name}}
              </a>
            <ul class="sub-menu d-block" style="list-style-position: inside;">
              {{-- Network-Clusters are Cached for 5 minutes --}}
              @foreach ($network->get_all_cluster_to_net() as $cluster)
                <li id="{{$cluster->name}}" class="has-sub">
                  <a href="{{BaseRoute::get_base_url()}}/Tree/erd/cluster/{{$cluster->id}}" style="width: 100%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;">
                    <i class="fa fa-circle-thin text-info"></i>
                    {{$cluster->name}}
                  </a>
                </li>
              @endforeach
            </ul>
          </li>
        </ul>
      </li>
      @endforeach

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
<div class="sidebar-bg"></div>


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
