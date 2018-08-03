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
          <span cl>Dashboard</span></a>
        </li>
      @endif

      <li class="nav-header">Navigation</li>
      @foreach ($view_header_links as $module_name => $typearray)
        @if (isset($typearray['submenu']))
        <li id="{{ Str::slug($module_name,'_')}}" class="has-sub" data-sidebar="level1">
          <a href="javascript:;">
            <i class="fa fa-fw {{ $typearray['icon'] }}"></i>
            <b class="caret pull-right"></b>
            <span>{{$module_name}}</span>
          </a>
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
    </ul>
    @can('view', Modules\HfcBase\Entities\TreeErd::class)
    <ul class="nav">
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
            </li>
            <ul class="sub-menu d-block" style="list-style-position: inside;">
              @foreach ($network->get_all_cluster_to_net() as $cluster)
                <li id="{{$cluster->name}}" class="has-sub">
                  <a href="{{BaseRoute::get_base_url()}}/Tree/erd/cluster/{{$cluster->id}}" style="width: 100%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;">
                    <i class="fa fa-circle-thin text-info"></i>
                    {{$cluster->name}}
                  </a>
                </li>
              @endforeach
            </ul>
          </ul>
        </li>
      @endforeach
    </ul>
    @endcan
    {{-- sidebar minify button --}}
    <li>
      <a href="javascript:;" class="sidebar-minify-btn hidden-xs" data-click="sidebar-minify">
      <i class="fa fa-angle-double-left"></i>
      </a>
    </li>
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
