<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
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
      <li class="nav-header" style="border-bottom: 1px solid; font-size: 13px !important; width: 223px;">
        <a href="{{route('Apps.active')}}" class="text-success d-inline w-100" style="display: inline-block !important; width: 100%;">{{ trans('messages.nativeApps') }}
          <i class="fa fa-plus"></i>
        </a>
      </li>
      {{-- Main Menu --}}
      @foreach ($view_header_links as $module_name => $typearray)
        @php
          $moduleNameSlug = Str::slug($module_name, '_');
        @endphp
        <li id="{{ $moduleNameSlug }}"
          class="has-sub"
          :class="{active: (lastActive == '{{ $moduleNameSlug }}'), 'position-relative': minified}">
          <div class="recolor sidebar-element" v-on:click="setMenu('{{ $moduleNameSlug }}')" v-on:mouseover="minified ? setMenu('{{ $moduleNameSlug }}') : ''" v-on:mouseLeave="leaveMinifiedSidebar">
            <a class="caret-link" href="{{ isset($typearray['link']) ?route($typearray['link']) : 'javascript:;'}}">
              @if (is_file(public_path('images/apps/').$typearray['icon']))
                <img src="{{ asset('images/apps/'.$typearray['icon']) }}" class="m-r-5" style="height: 20px; margin-right: 7px; filter: saturate(25%) brightness(80%);">
              @else
                <i class="fa fa-fw {{ $typearray['icon'] }} m-r-5"></i>
              @endif
              <span>{{$typearray['translated_name'] ?? $module_name}}</span>
            </a>
            @if(isset($typearray['submenu']))
              <a class="caret-link" href="javascript:;" style="width: 100%; height: 20px; display:block; text-align: right">
                <i class="fa fa-caret-right" :class="showSubMenu('{{ $moduleNameSlug }}') ? 'fa-rotate-90' : ''" style="transition:all .25s;"></i>
              </a>
            @endif
          </div>
        {{-- SubMenu --}}
        @isset ($typearray['submenu'])
          <transition name="accordion" v-on:before-enter="beforeEnter" v-on:enter="enter" v-on:before-leave="beforeLeave" v-on:leave="leave">
            <ul v-show="showSubMenu('{{ $moduleNameSlug }}')" class="sidebar-hover p-b-10 p-l-20 m-0" :class="{'minifiedMenu': (showMinifiedHoverMenu && showSubMenu('{{ $moduleNameSlug }}', true))}" style="transition:all .3s linear;overflow:hidden;list-style-type: none;background: #1a2229;display:none;">
            @foreach ($typearray['submenu'] as $type => $valuearray)
            <li id="menu-{{ Str::slug($type,'_') }}" v-on:click="setSubMenu('menu-{{ Str::slug($type,'_') }}')" class="{{ $loop->first ? 'p-t-10' : ''}}" :class="{active: (lastClicked == 'menu-{{ Str::slug($type,'_') }}')}" v-on:mouseover="minified ? setMenu('{{ $moduleNameSlug }}') : ''" v-on:mouseLeave="showMinifiedHoverMenu = false">
              <a href="{{ route($valuearray['link']) }}" style="display:block;padding:5px 20px;color:#889097;overflow: hidden;white-space:nowrap;font-weight:300;text-decoration:none;">
                <i class="fa fa-fw {{ $valuearray['icon'] }}"></i>
                <span>{{ $type }}</span>
              </a>
            </li>
            @endforeach
            </ul>
          </transition>
        @endisset
        {{-- End SubMenu--}}
        </li>
      @endforeach
      {{-- End Menu --}}

    <li class="nav-header" style="border-top: 1px solid; font-size: 13px !important; width: 223px;">
      <a href="{{route('Apps.active')}}" class="text-success d-inline w-100" style="display: inline-block !important; width: 100%;">{{ trans('messages.externalApps') }}
        <i class="fa fa-plus"></i>
      </a>
    </li>
    @foreach ($externalApps as $appName => $externalApp)
      @if ($externalApp['state'] == 'active' && file_exists(public_path('images/'.$externalApp['icon'])))
        <li>
          <div class="sidebar-element recolor">
            <a href="{{ $externalApp['link'] }}">
                <img src="{{ asset('images/'.$externalApp['icon']) }}" class="external-app-mini h-20 w-20 m-r-5">
                <span>{{ $appName }}</span>
            </a>
          </div>
        </li>
      @endif
    @endforeach

    @if(Module::collections()->has('HfcBase') && auth()->user()->can('view', Modules\HfcBase\Entities\TreeErd::class))
      <li v-show="!minified" class="nav-header align-items-center no-content-pseudo" style="border-top:1px solid;font-size:13px;width: 100%;display:flex;justify-content:space-between;">
        <div class="text-success">{{ trans('view.Menu_Nets') }}</div>
        <div class="d-flex">
          <div v-if="! isSearchMode" style="cursor:pointer;" class="text-success p-r-10" v-on:click="isVisible = !isVisible"><i class="fa" :class="isVisible ? 'fa-eye' : 'fa-eye-slash'"></i></div>
          <div v-if="! isSearchMode" style="cursor:pointer;" class="text-success p-r-10" data-toggle="modal" data-target="#exampleModalCenter"><i class="fa fa-pencil"></i></div>
          <div style="cursor:pointer;" class="text-success" v-on:click="isSearchMode = !isSearchMode"><i class="fa fa-search"></i></div>
        </div>
      </li>
      <div v-if="isSearchMode" class="my-1 d-flex align-items-center" style="padding:0.5rem 1.25rem;">
        <input type="text" class="form-control position-relative" style="padding-right:2.5rem;" placeholder="Search ..." aria-label="Search ..." aria-describedby="Search for Net or Cluster">
        <div class="position-absolute btn bg-transparent" type="button" style="right:20px;padding: 0 .5rem;"><i class="fa fa-arrow-circle-right m-0"></i></div>
      </div>
      <div v-show="isVisible">
        @if (auth()->user()->isAllNetsSidebarEnabled)
          <li id="network_overview" class="has-sub">
            <div class="recolor" style="display: flex;justify-content:space-between;padding: 0.5rem 1.25rem;line-height: 20px;">
              <a href="{{ route('TreeErd.show', ['field' => 'all', 'search' => 1]) }}" style="max-height: 20px; white-space: nowrap;">
                <i class="fa fa-sitemap m-r-5"></i>
                <span>{{ trans('view.Menu_allNets') }}</span>
              </a>
            </div>
          </li>
        @endif
        @foreach ($networks as $network)
          <li id="network_{{$network->id}}" class="has-sub">
            <div class="recolor" style="display: flex;justify-content:space-between;padding: 0.5rem 1.25rem;">
              <a href="{{ route('TreeErd.show', ['field' => 'net', 'search' => $network->id]) }}" style="max-height: 20px; white-space: nowrap;">
                <i class="fa fa-sitemap m-r-5"></i>
                <span>{{$network->name}}</span>
              </a>
              {{-- @if($network->clusters->isNotEmpty()) --}}
                <a class="caret-link" style="width: 100%; text-align: right;" href="javascript:;">
                  <b class="caret fa-rotate-270"></b>
                </a>
              {{-- @endif --}}
            </div>
            <ul class="sub-menu line sub-line" style="display: none;padding: 0;">
              {{-- Network-Clusters are Cached for 5 minutes --}}
              {{-- @foreach ($network->clusters as $cluster)
                <li id="cluster_{{$cluster->id}}">
                  <a href="{{ route('TreeErd.show', ['field' => 'cluster', 'search' => $cluster->id]) }}" style="width: 100%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;">
                    <i class="fa fa-circle-thin text-info"></i>
                    {{$cluster->name}}
                  </a>
                </li>
              @endforeach --}}
            </ul>
          </li>
        @endforeach
      </div>
      <div id="searchresults">

      </div>
    @endif
    {{-- sidebar minify button --}}
    <li>
      <a href="javascript:;" class="sidebar-minify-btn hidden-xs hover-not-supported" v-on:click="handleMinify" data-click="sidebar-minify">
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
