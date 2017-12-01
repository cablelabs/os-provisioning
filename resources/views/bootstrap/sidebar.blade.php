 {{-- begin #sidebar --}}
    <div id="sidebar" class="sidebar">
     {{-- begin sidebar scrollbar --}}
      <div data-scrollbar="true" data-height="100%">
       {{-- begin sidebar user --}}
        <ul class="nav">
          <li class="nav-profile">
            <div class="info">
              {{ $framework['header2'] }}
              <small>Version {{ $framework['version'] }}</small>
            </div>
          </li>
        </ul>
       {{-- end sidebar user --}}

       {{-- begin sidebar nav --}}
        <ul class="nav">
          @if (\PPModule::is_active ('Dashboard'))
            <li id ="dashboardsidebar"
              @if ($route_name == "Dashboard")
                class="active"
              @endif
              >
              <a href="{{BaseRoute::get_base_url()}}/Dashboard">
              <i class="fa fa-home"></i>
              <span cl>Dashboard</span></a>
            </li>
          @endif

          <li class="nav-header">Navigation</li>
          @foreach ($view_header_links as $module_name => $typearray)
            @if (isset($typearray['submenu']))
            <li id="{{ Str::slug($module_name,'_')}}" class="has-sub">
              <a href="javascript:;">
                <i class="fa fa-fw {{ $typearray['icon'] }}"></i>
                <b class="caret pull-right"></b>
                <span>{{$module_name}}</span>
              </a>
              <ul class="sub-menu">
              @foreach ($typearray['submenu'] as $type => $valuearray)
              <li id="{{  Str::slug($type,'_') }}">
                <a href="{{route($valuearray['link'])}}">
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

        <ul class="nav">
         <li class="nav-header">Networks</li>

          @foreach ($networks as $network)
            <li class="has-sub">
              <a href="javascript:;">
                <i class="fa fa-sitemap"></i>
                <b class="caret pull-right"></b>
                <span>{{$network->name}}</span>
              </a>
              <ul class="sub-menu">
                <li><a href="{{BaseRoute::get_base_url()}}/Tree/erd/net/{{$network->id}}"><i class="fa fa-circle"></i> {{$network->name}}</a></li>
                @foreach ($network->get_all_cluster_to_net() as $cluster)
                 <li><a href="{{BaseRoute::get_base_url()}}/Tree/erd/cluster/{{$cluster->id}}"><i class="fa fa-circle-thin"></i> {{$cluster->name}}</a></li>
                @endforeach
              </ul>
            </li>
         @endforeach

         {{-- begin sidebar minify button --}}
          <li>
            <a href="javascript:;" class="sidebar-minify-btn hidden-xs" data-click="sidebar-minify">
            <i class="fa fa-angle-double-left"></i>
            </a>
          </li>
         {{-- end sidebar minify button --}}
        </ul>
       {{-- end sidebar nav --}}
      </div>
     {{-- end sidebar scrollbar --}}
      </div>
    <div class="sidebar-bg"></div>
   {{-- end #sidebar --}}


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
