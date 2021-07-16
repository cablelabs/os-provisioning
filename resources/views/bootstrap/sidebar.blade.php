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
<div v-cloak id="sidebar" class="sidebar d-print-none">
  {{-- begin sidebar scrollbar --}}
  <div data-scrollbar="true" data-height="100%">
    {{-- begin sidebar user --}}
    <ul class="nav">
      <li class="nav-profile">
        <div class="info">
          {{ $globalConfig->headline2 }}
          <small>Version
            @if (is_string($version))
              {{ $version }}
            @else
              <b>GIT</b>: <a target="_blank" class="text-success" href="https://github.com/{{ $version['repo'] }}/tree/{{ $version['branch'] }}">{{ $version['branch'] }}</a> -
              <a target="_blank" class="text-success" href="https://github.com/{{ $version['repo'] }}/commit/{{ $version['commitLong'] }}">{{ $version['commitShort'] }}</a>
            @endif
          </small>
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
        <li id="{{ $moduleNameSlug }}" class="has-sub"
          :class="{active: (lastActive == '{{ $moduleNameSlug }}'), 'position-relative': minified}"
          style="z-index:10000;">
          <div class="recolor sidebar-element"
            v-on:mouseEnter="minified ? setMenu('{{ $moduleNameSlug }}') : ''"
            v-on:mouseLeave="minified ? leaveMinifiedSidebar() : ''">
            <a class="caret-link"
              href="{{ isset($typearray['link']) ?route($typearray['link']) : 'javascript:;'}}">
              @if (is_file(public_path('images/apps/').$typearray['icon']))
                <img src="{{ asset('images/apps/'.$typearray['icon']) }}" class="m-r-5" style="height: 20px; margin-right: 7px; filter: saturate(25%) brightness(80%);">
              @else
                <i class="fa fa-fw {{ $typearray['icon'] }} m-r-5"></i>
              @endif
              <span>{{$typearray['translated_name'] ?? $module_name}}</span>
            </a>
            @if(isset($typearray['submenu']))
              <a class="caret-link" href="javascript:;" v-on:click.stop="setMenu('{{ $moduleNameSlug }}')" style="width: 100%; height: 20px; display:block; text-align: right">
                <i class="fa fa-caret-right" :class="{'fa-rotate-90': showSubMenu('{{ $moduleNameSlug }}')}" style="transition:all .25s;"></i>
              </a>
            @endif
          </div>
        {{-- SubMenu --}}
        @isset ($typearray['submenu'])
          <transition name="accordion" v-on:before-enter="beforeEnter" v-on:enter="enter" v-on:before-leave="beforeLeave" v-on:leave="leave" v-on:after-leave="afterLeave">
            <ul v-show="showSubMenu('{{ $moduleNameSlug }}')" class="sidebar-hover pl-0 p-b-10 m-0" :class="{'minifiedMenu': (showMinifiedHoverMenu && showSubMenu('{{ $moduleNameSlug }}', true))}" style="transition:all .3s linear;overflow:hidden;list-style-type: none;background: #1a2229;display:none;">
            @foreach ($typearray['submenu'] as $type => $valuearray)
            <li id="menu-{{ Str::slug($type,'_') }}"
              class="p-l-20 {{ $loop->first ? 'p-t-10' : ''}}"
              :class="{active: (lastClicked == 'menu-{{ Str::slug($type,'_') }}')}"
              v-on:click="setSubMenu('menu-{{ Str::slug($type,'_') }}')"
              v-on:mouseEnter.stop="minified ? setMenu('{{ $moduleNameSlug }}') : ''"
              v-on:mouseLeave.stop="minified ? leaveMinifiedSidebar() : ''">
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
      <li v-cloak v-show="!minified" class="nav-header align-items-center no-content-pseudo" style="border-top:1px solid;font-size:13px;width: 100%;display:flex;justify-content:space-between;">
        <div class="text-success" style="flex:1;">{{ trans('view.Menu_Nets') }}</div>
        <div class="m-r-15" v-on:click.stop="setVisibility">
          <i class="text-light fa" :class="isVisible ? 'fa-eye' : 'fa-eye-slash'" style="cursor: pointer;"></i>
        </div>
        <div class="d-flex align-items-center position-relative" :style="'cursor:pointer;background: #232a2f;border-radius: 9999px;width:3.25rem;height:1.35rem;transition: width .25s;' + (!isVisible ? 'opacity:0;width:0;' : '')" v-on:click="setSearchMode">
          <div class="position-absolute" :style="'background: #8ec73a;border-radius: 9999px;width:1.2rem;height:1.2rem;transition: all .25s;' + ((isSearchMode) ? 'left:31.5px;' : 'left:2px;')"></div>
          <div class="position-absolute" :style="((!isSearchMode) ? 'left:5px;color: #fff;' : 'left:5px;')"><i class="m-0 fa" :class="favorites.length ? 'fa-star' : 'fa-sitemap'"></i></div>
          <div class="position-absolute" :style="'right:6px;' + ((isSearchMode) ? 'color: #fff;' : '')"><i class="m-0 fa fa-search"></i></div>
        </div>
      </li>
      <div v-show="isSearchMode && isVisible" class="my-1 d-flex align-items-center position-relative" style="padding:0.5rem 1.25rem;display:none;">
        <input type="text" v-model="clusterSearch" v-on:keyup="searchForNetOrCluster" class="form-control" style="padding-left:2rem;" placeholder="{{ trans('view.Search_EnterKeyword') }} ..." aria-label="Search ..." aria-describedby="Search for Net or Cluster">
        <i class="fa fa-search position-absolute" style="left:30px;" ></i>
      </div>
      <template v-if="isVisible">
        @if ($globalConfig->isAllNetsSidebarEnabled)
          <li id="network_overview" class="has-sub">
            <div class="recolor sidebar-element">
              <a href="{{ route('TreeErd.show', ['field' => 'all', 'search' => 1]) }}" style="max-height: 20px; white-space: nowrap;">
                <i class="fa fa-sitemap m-r-5"></i>
                <span>{{ trans('view.Menu_allNets') }}</span>
              </a>
            </div>
          </li>
        @endif
        <template v-for="netelement in loopNetElements">
          <li class="has-sub" :key="netelement.id">
            <div v-cloak class="recolor sidebar-element"
              v-on:mouseEnter.stop="minified ? loadCluster(netelement) : ''"
              v-on:mouseLeave.stop="minified ? leaveMinifiedSidebar(netelement) : ''"
              style="display: flex;padding: 0.5rem 1.25rem;">
              <template v-if="isSearchMode" style="cursor: pointer;">
                <a href="javascript:;" >
                  <i v-if="loadingFavorites.includes(netelement.id)" class="caret-link fa fa-circle-o-notch fa-spin m-r-5"></i>
                  <i v-else class="caret-link fa m-r-5" :class="favorites.includes(netelement.id) ? 'fa-star' : 'fa-star-o'" v-on:click="favorNetelement(netelement)"></i>
                </a>
                <a :href="'/admin/Tree/erd/' + (netelement.netelementtype_id == 1 ? 'net/' : 'cluster/') + netelement.id" style="max-height: 20px; white-space: nowrap;flex:1;">
                  <span v-text="netelement.name"></span>
                </a>
              </template>
              <a v-else :href="'/admin/Tree/erd/' + (netelement.netelementtype_id == 1 ? 'net/' : 'cluster/') + netelement.id" class="caret-link" style="max-height: 20px; white-space: nowrap;flex:1;">
                <i v-on:mouseenter="setHover(netelement, true)" v-on:mouseLeave="setHover(netelement, false)" v-on:click="directFavor(netelement, $event)" class="fa m-r-5" :class="netelement.hover ? (favorites.includes(netelement.id) ? 'fa-star' : 'fa-star-o') : 'fa-sitemap'"></i>
                <span v-text="netelement.name"></span>
              </a>
              <div v-if="netelement.netelementtype_id == 1" v-on:click="loadCluster(netelement)" class="caret-link" style="cursor: pointer;width: 100%; text-align: right;">
                <i v-if="loadingClusters.includes(netelement.id)" class="fa fa-circle-o-notch fa-spin"></i>
                <i v-else class="fa fa-caret-right" :class="{'fa-rotate-90': !netelement.isCollapsed && !minified}" style="transition:all .25s;"></i>
              </div>
            </div>
            <transition name="accordion" v-on:before-enter="beforeEnter" v-on:enter="enter" v-on:before-leave="beforeLeave" v-on:leave="leave" v-on:after-leave="afterLeave">
              <ul :id="'network_' + netelement.id" v-if="netelement.netelementtype_id == 1 && netelement.clustersLoaded && !netelement.isCollapsed" class="sidebar-hover p-b-10 p-l-20 m-0" :class="{'minifiedMenu': (showMinifiedHoverNet && netelement.clustersLoaded)}" :style="(!minified ? 'transition:max-height .3s linear;' : '') + 'overflow:hidden;list-style-type: none;background: #1a2229;'">
                <template v-for="cluster in netelement.clusters" >
                  <li :id="'cluster_' + cluster.id"
                    :key="cluster.id"
                    v-on:click="clickedNetelement = cluster.name"
                    :class="{active: (clickedNetelement== cluster.name), 'p-t-10': (netelement.clusters[0].id == cluster.id)}"
                    v-on:mouseEnter.stop="minified ? minifiedSidebarNet(netelement, 'enter') : ''"
                    v-on:mouseLeave.stop="minified ? minifiedSidebarNet(netelement, 'leave') : ''">
                    <a :href="'/admin/Tree/erd/cluster/' + cluster.id" style="display:block;padding:5px 20px;color:#889097;overflow: hidden;white-space:nowrap;font-weight:300;text-decoration:none;">
                      <i class="fa fa-circle-thin text-info"></i>
                      <span v-text="cluster.name"></span>
                    </a>
                  </li>
                </template>
              </ul>
            </transition>
          </li>
        </template>
        <li v-if="Object.keys(loopNetElements).length === 0 && !isSearchMode && !minified" class="m-l-20 m-t-10 text-light w-75">{{ trans('messages.refreshPage')}}</li>
      </template>
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
<div class="sidebar-bg d-print-none" style="transition: all .25s ease-in-out"></div>


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

 <script language="javascript">
  if (typeof(Storage) === "undefined") {
    console.error("Sorry, no Web Storage Support - Cant save State of Sidebar - please update your Browser")
  }

  if (localStorage.getItem('minified-state') === 'true') {
    document.getElementById('page-container').classList.add('page-sidebar-minified')
  } else {
    document.getElementById('page-container').classList.remove('page-sidebar-minified')
  }
</script>
