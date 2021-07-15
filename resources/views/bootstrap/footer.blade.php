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
<!-- ================== BEGIN BASE JS ================== -->
<script src="{{asset('components/assets-admin/plugins/jquery/jquery-3.2.0.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/jquery/jquery-migrate-1.4.1.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/vue/dist/vue.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/Abilities/axios.min.js')}}"></script>

{{-- When in Development use this Version
    <script src="{{asset('components/assets-admin/plugins/vue/dist/vue.js')}}"></script>
--}}

<script src="{{asset('components/assets-admin/plugins/vue/dist/vue.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/Abilities/axios.min.js')}}"></script>

{{-- When in Development use this Version
  <script src="{{asset('components/assets-admin/plugins/vue/dist/vue.min.js')}}"></script>
--}}

<script src="{{asset('components/assets-admin/plugins/bootstrap4/js/bootstrap.bundle.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/select2/dist/js/select2.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/select2/dist/js/i18n/'.App::getLocale().'.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/main/media/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/main/media/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/responsive-bootstrap/js/responsive.bootstrap4.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/fixedHeader/js/dataTables.fixedHeader.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/fixedHeader-bootstrap/js/fixedHeader.bootstrap4.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/jstree/dist/jstree.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/chart/Chart.min.js')}}"></script>

<!--[if lt IE 9]>
  <script src="{{asset('components/assets-admin/crossbrowserjs/html5shiv.js')}}"></script>
  <script src="{{asset('components/assets-admin/crossbrowserjs/respond.min.js')}}"></script>
  <script src="{{asset('components/assets-admin/crossbrowserjs/excanvas.min.js')}}"></script>
<![endif]-->
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons-bootstrap/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons/js/buttons.colVis.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/ionRangeSlider/js/ion.rangeSlider.js')}}"></script>


<script src="{{asset('components/assets-admin/js/apps.js')}}"></script>
<script src="{{asset('components/nmsprime.js')}}"></script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script language="javascript">
/*
 * global document ready function
 */
$(document).ready(function() {
  App.init();
  NMS.init();
  {{-- init modals --}}
  $("#alertModal").modal();
});

new Vue({
  el: '#sidebar',
  mounted() {
    if (typeof(Storage) === "undefined") {
      console.error("Sorry, no Web Storage Support - Cant save State of Sidebar - please update your Browser")
    }

    this.initSidebar()
  },
  data() {
    return {
      {{-- route: '{{$route_name}}', --}}
      minified: null,
      leaveTimer: null,
      showMinifiedHoverMenu: false,
      showMinifiedHoverNet: false,
      isVisible: true,
      isSearchMode: false,
      isCollapsed: true,
      isLoading: [],
      lastActive: 'null',
      lastClicked: 'null',
      activeItem: 'null',
      clickedItem: 'null',
      searchTimeout: null,
      clusterSearch: '',
      searchResults: {},
      activeNetelement: 'null',
      clickedNetelement: 'null',
      netelementIsCollapsed: true,
      netelements: @json($networks ?? new stdClass()),
      favorites: @json($favorites ?? new stdClass()),
    }
  },
  computed: {
    loopNetElements() {
      if (this.isSearchMode) {
        return this.searchResults
      }

      return this.netelements
    }
  },
  methods: {
    initSidebar() {
      this.minified = localStorage.getItem('minified-state') === 'true'
      this.isVisible = localStorage.getItem('sidebar-net-isVisible') === 'true'
      this.isSearchMode = localStorage.getItem('sidebar-net-isSearchMode') === 'true'
      this.clusterSearch = localStorage.getItem('sidebar-net-search')
      this.searchResults = JSON.parse(localStorage.getItem('sidebar-net-searchResults'))
      this.lastActive = this.activeItem = localStorage.getItem('sidebar-item')
      this.lastClicked = this.clickedItem = localStorage.getItem('clicked-item')
      this.activeNetelement = localStorage.getItem('sidebar-net')
      this.clickedNetelement = localStorage.getItem('clicked-netelement')
      this.isCollapsed = false
    },
    handleMinify(e) {
      let sidebar = document.getElementById('sidebar')
      let pageContainer = document.getElementById('page-container')

      sidebar.style.marginTop = 0
      sidebar.style.overflow = 'visible'
      sidebar.removeAttribute('data-init')

      if (! this.minified) {
        pageContainer.classList.add('page-sidebar-minified')
      }

      if (this.minified) {
        pageContainer.classList.remove('page-sidebar-minified')
        $(sidebar).slimScroll({destroy: true})
      }

      if(! /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        $(sidebar).slimScroll({destroy: true})
      }

      sidebar.style.transition = 'all .15s ease-in-out'
      sidebar.style.width = this.minified ? '220px' : '60px'
      this.minified = ! this.minified
      this.showMinifiedHoverMenu = this.minified
      this.isCollapsed = true
      this.netelements.forEach(n => n.isCollapsed = false)

      if (this.isVisible && this.isSearchMode) {
        this.setSearchMode()
      }

      localStorage.setItem('minified-state', this.minified)
      setTimeout(() => {
        $(window).trigger('resize')
        if ($('table.datatable').length && ! this.minified) {
          $('table.datatable').DataTable().responsive.recalc()
        }
      }, 200)
    },
    leaveMinifiedSidebar(netelement = 'null') {
      if (netelement !== 'null') {
        return this.leaveTimer = setTimeout(() => {
          this.netelements.forEach(n => n.isCollapsed = false)
          this.showMinifiedHoverNet = false
        }, 250)
      }

      this.leaveTimer = setTimeout(() => {this.showMinifiedHoverMenu = false; }, 250)
    },
    toggleNetMinified(netelement) {
      clearTimeout(this.leaveTimer)
      this.netelements.forEach((n) => n.isCollapsed = n.id == netelement.id)
      this.showMinifiedHoverNet = true
    },
    minifiedSidebarNet(netelement, type) {
      if (! this.minified) {
        return;
      }

      if (type == 'enter') {
        this.showMinifiedHoverNet = true
        netelement.isCollapsed = true
        return clearTimeout(this.leaveTimer)
      }

      this.leaveMinifiedSidebar(netelement)
    },
    setVisibility() {
      this.isVisible = !this.isVisible

      localStorage.setItem('sidebar-net-isVisible', this.isVisible)
    },
    setSearchMode() {
      this.isSearchMode = !this.isSearchMode

      localStorage.setItem('sidebar-net-isSearchMode', this.isSearchMode)
    },
    setMenu(name) {
      if (name === this.activeItem && ! this.minified) {
        return this.isCollapsed = ! this.isCollapsed
      }

      this.activeItem = name
      this.clickedItem = name
      this.isCollapsed = false

      if (this.minified) {
        clearTimeout(this.leaveTimer)
        this.showMinifiedHoverMenu = true
      }

      localStorage.setItem("sidebar-item", name)
      localStorage.setItem("clicked-item", name)
    },
    setSubMenu(name) {
      this.clickedItem = name
      localStorage.setItem("clicked-item", name)
    },
    showSubMenu(name, minified = false) {
      if (! minified) {
        return this.activeItem == name && ! this.isCollapsed && ! this.minified
      }

      return this.activeItem == name && ! this.isCollapsed
    },
    beforeEnter(el) {
      el.style.maxHeight = '0'
    },
    enter(el) {
      el.style.maxHeight = el.scrollHeight + 'px'
    },
    beforeLeave(el) {
      el.style.maxHeight = el.scrollHeight + 'px'
    },
    leave(el) {
      el.style.maxHeight = '0'
    },
    afterLeave(el) {
      el.style.maxHeight = 1000 + 'px'
    },
    searchForNetOrCluster(event) {
      clearTimeout(this.searchTimeout)
      localStorage.setItem('sidebar-net-search', this.clusterSearch)

      this.searchTimeout = setTimeout(() => {
        axios({
            method: 'post',
            url: '/admin/Netelement/netclustersearch',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            data: {
              query: this.clusterSearch
            }
        })
        .then((response) => {
            this.searchResults = response.data

            localStorage.setItem('sidebar-net-searchResults', JSON.stringify(response.data))
        })
        .catch((error) => {
            console.error(error)
            this.$snotify.error(error.message)
        })
      }, 500)
    },
    loadClusters(netelement) {
      if (this.minified) {
        this.toggleNetMinified(netelement)
      } else {
        netelement.isCollapsed = !netelement.isCollapsed
      }

      if (netelement.clustersLoaded) {
        return
      }

      this.isLoading.splice(0, 0, netelement.id)

      axios({
        method: 'post',
        url: '/admin/Netelement/' + netelement.id + '/clustersearch',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
      })
      .then((response) => {
        netelement.clustersLoaded = true
        this.isLoading.splice(this.isLoading.indexOf(netelement.id), 1)
        if (this.isSearchMode) {
          this.searchResults[this.searchResults.findIndex(n => n.id === netelement.id)].clusters = response.data
          return this.searchResults = jQuery.extend(true, {}, this.searchResults);
        }

        this.netelements[this.netelements.findIndex(n => n.id === netelement.id)].clusters = response.data
        this.netelements = jQuery.extend(true, [], this.netelements)

      })
      .catch((error) => {
          console.error(error)
          this.$snotify.error(error.message)
      })
    },
    favorNetelement(netelement) {
      axios({
        method: 'post',
        url: '/admin/Netelement/' + netelement.id + '/' + (this.favorites.includes(netelement.id) ? 'unfavorite' : 'favorite'),
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
      })
      .then(() => {
        if (this.favorites.includes(netelement.id)) {
          return this.favorites.splice(this.favorites.indexOf(netelement.id), 1)
        }

        this.favorites.splice(this.favorites.length, 0, netelement.id)
      })
      .catch((error) => {
          console.log(error)
          this.$snotify.error(error.message)
      })
    }
  }
})

</script>
