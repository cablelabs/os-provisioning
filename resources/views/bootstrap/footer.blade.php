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
<script src="{{asset('components/assets-admin/plugins/vue-snotify/snotify.min.js')}}"></script>

{{-- When in Development use this Version
  <script src="{{asset('components/assets-admin/plugins/vue/dist/vue.js')}}"></script>
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

<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons-bootstrap/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('components/assets-admin/plugins/DataTables/extensions/buttons/js/buttons.colVis.min.js')}}"></script>

<script src="{{asset('components/assets-admin/plugins/ionRangeSlider/js/ion.rangeSlider.js')}}"></script>


<script src="{{asset('components/assets-admin/js/apps.js')}}"></script>
<script src="{{asset('components/nmsprime.js?ver=211215')}}"></script>
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

Vue.component("select2", {
    props: {
      options: [Object, Array],
      initialValue: [String, Number],
      multiple: {
        type: Boolean,
        default: false
      },
      asArray: {
        type: Boolean,
        default: false
      }
    },
    template: "#select2-template",
    mounted() {
        this.select = $(this.$el)
        this.value = this.multiple || this.asArray ? [this.initialValue] : this.initialValue

        this.select.select2({
          data: this.options,
          multiple: this.multiple
        }).val(this.value)
        .trigger('change')

        if (! this.multiple) {
          return this.select.on('change', (e) => this.$emit("input", e.target.value))
        }

        this.select.on('select2:select', (e) => this.selected(e.params.data.id))
        this.select.on('select2:unselect', (e) => this.unselected(e.params.data.id))
    },
    data() {
      return {
        select: undefined,
        value: undefined,
        i18nAll: '{{ trans('messages.all') }}'
      }
    },
    methods: {
      selected: function (value) {
        if (value == this.i18nAll) {
          this.value = []
        }

        if (value != this.i18nAll && this.value.includes(this.i18nAll)) {
          this.value.splice(this.value.indexOf(this.i18nAll), 1)
        }

        this.value.push(value)
        this.publishChanges()
      },
      unselected: function (value) {
        if (value == this.i18nAll) {
          return this.$emit("input", [])
        }

        this.value.splice(this.value.indexOf(value), 1)
        this.publishChanges()
      },
      publishChanges: function () {
        this.$emit("input", this.value)
        this.select.val(this.value).trigger("change")
      }
    },
    watch: {
        options: function(options) {
          this.select.empty().select2({ data: options })
        }
    },
    destroyed() {
      this.select.off().select2("destroy")
    }
})

new Vue({
  el: '#sidebar',
  mounted() {
    if (typeof(Storage) === "undefined") {
      console.error("Sorry, no Web Storage Support - Cant save State of Sidebar - please update your Browser")
    }

    if (document.getElementById('sidebar')) {
      this.initSidebar()
    }
  },
  data() {
    return {
      minified: null,
      leaveTimer: null, // timer for leave and enter minified menu
      showMinifiedHoverMenu: false,
      showMinifiedHoverNet: false,
      isVisible: true, // show nets?
      isSearchMode: false,
      initialNE: true, // first 25 Nets - default
      isCollapsed: true, // show submenu: ul is extended (also for minified)
      loadingClusters: [], // loading circle for clusters
      loadingFavorites: [], // loading circle for favoriting
      loadingSearch: false, // loading circle for search
      lastActive: 'null',
      lastClicked: 'null',
      activeItem: 'null',
      clickedItem: 'null',
      searchTimeout: null, // debounce for search (500ms)
      clusterSearch: '', // v-model for search
      searchResults: [],
      activeNetelement: null,
      clickedNetelement: null,
      netelements: @json($networks ?? new stdClass()),
      netCount: {{ $netCount ?? 0 }},
      favorites: @json($favorites ?? new stdClass()),
    }
  },
  computed: {
    loopNetElements() { // variable to hold nets of either search or favorites
      if (this.isSearchMode) {
        return this.searchResults
      }

      return this.netelements
    }
  },
  methods: {
    initSidebar() {
      this.initialNE = this.favorites.length === 0

      // load minified state
      this.minified = localStorage.getItem('minified-state') === 'true'
      // load state of Net toggle
      this.isVisible = this.netCount ? localStorage.getItem('sidebar-net-isVisible') === 'true' : false
      this.isSearchMode = localStorage.getItem('sidebar-net-isSearchMode') === 'true'

      // load cached search term and results
      if(localStorage.getItem('sidebar-net-search') !== null) {
        this.clusterSearch = localStorage.getItem('sidebar-net-search')
      }

      if(localStorage.getItem('sidebar-net-searchResults') !== null) {
        this.searchResults = JSON.parse(localStorage.getItem('sidebar-net-searchResults'))
      }

      // init Sidebar active/clicked elements
      this.lastActive = this.activeItem = localStorage.getItem('sidebar-item')
      this.lastClicked = this.clickedItem = localStorage.getItem('clicked-item')
      this.activeNetelement = localStorage.getItem('sidebar-net')
      this.clickedNetelement = this.activeNetelement ? localStorage.getItem('clicked-netelement') : null

      // init collapse
      this.isCollapsed = false
      this.netelements.forEach(n => {
        n.isCollapsed = true

        if (this.activeNetelement &&  this.clickedNetelement && n.id == this.activeNetelement && !this.isSearchMode) {
          this.loadCluster(n)
        }
      })
    },
    /**
    * Handles the Click on the minify button. A lot of the logic copied from
    * color admin due to conflicts.
    */
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
      this.netelements.forEach(n => n.isCollapsed = true)

      if (this.isVisible && this.isSearchMode) {
        this.setSearchMode()
      }

      localStorage.setItem('minified-state', this.minified)
      setTimeout(() => {
        $(window).trigger('resize')
        if ($('table.datatable').length && ! this.minified) { // resize datatables after animation
          $('table.datatable').DataTable().responsive.recalc()
        }
      }, 200)
    },
    /**
    * Handles the mouse leave of the minified menu for net and main menu.
    */
    leaveMinifiedSidebar(netelement = 'null') {
      if (netelement !== 'null') {
        return this.leaveTimer = setTimeout(() => {
          this.netelements.forEach(n => n.isCollapsed = true)
          this.showMinifiedHoverNet = false
        }, 250)
      }

      this.leaveTimer = setTimeout(() => {
        this.showMinifiedHoverMenu = false
        this.isCollapsed = true
      }, 250)
    },
    /**
    * Collapse all nets except the selected one. assuers that only one submenu
    * is visible to the user.
    */
    toggleNetMinified(netelement) {
      clearTimeout(this.leaveTimer)
      this.netelements.forEach((n) => n.isCollapsed = n.id !== netelement.id)
      this.showMinifiedHoverNet = true
    },
    /**
    * Handles the enter and leave of the minified net menu
    */
    minifiedSidebarNet(netelement, type) {
      if (! this.minified) {
        return;
      }

      if (type == 'enter') {
        this.showMinifiedHoverNet = true
        netelement.isCollapsed = false
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
    /**
    * Handles the Collapse and expand logic of the main menu (also for minified)
    */
    setMenu(name, collapse = true) {
      if (name === this.activeItem && ! this.minified && collapse) {
        return this.isCollapsed = ! this.isCollapsed
      }

      this.activeItem = name
      this.clickedItem = name
      this.isCollapsed = !collapse

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
    /**
    * The following methods handle the accordion animation
    */
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
    /**
    * Ajax Request for Search from Input. (isSearchMode: true)
    */
    searchForNetOrCluster(event) {
      clearTimeout(this.searchTimeout)
      this.loadingSearch = true
      localStorage.setItem('sidebar-net-search', this.clusterSearch)

      this.searchTimeout = setTimeout(() => {
        if(this.clusterSearch === '') {
          return
        }

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
          this.searchResults.forEach(n => n.isCollapsed = true)

          this.loadingSearch = false
          localStorage.setItem('sidebar-net-searchResults', JSON.stringify(response.data))
        })
        .catch((error) => {
            console.error(error)
            this.$snotify.error(error.message)
        })
      }, 500)
    },
    /**
    * Lazy load Clusters via AJAX and also handles expand/collapse Logic
    */
    loadCluster(netelement) {
      netelement.isCollapsed = !netelement.isCollapsed

      if (this.minified) {
        this.toggleNetMinified(netelement)
      }

      localStorage.setItem('sidebar-net', netelement.id)
      if(netelement.isCollapsed) {
        localStorage.removeItem('sidebar-net')
      }

      if (netelement.clustersLoaded) {
        return
      }

      this.loadingClusters.push(netelement.id)

      axios({
        method: 'post',
        url: '/admin/Netelement/' + netelement.id + '/clustersearch',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
      })
      .then((response) => {
        netelement.clustersLoaded = true
        this.loadingClusters.splice(this.loadingClusters.indexOf(netelement.id), 1)
        if (this.isSearchMode) {
          this.searchResults[this.searchResults.findIndex(n => n.id === netelement.id)].clusters = response.data
          this.searchResults = jQuery.extend(true, [], this.searchResults) // Object deep Copy is necessary to detect changes
          return localStorage.setItem('sidebar-net-searchResults', JSON.stringify(this.searchResults))
        }

        this.netelements[this.netelements.findIndex(n => n.id === netelement.id)].clusters = response.data

        if (this.loadingClusters.length === 0) {
          this.netelements = jQuery.extend(true, [], this.netelements)
        }
      })
      .catch((error) => {
          console.error(error)
          this.$snotify.error(error.message)
      })
    },
    /**
    * Helper function to simplify favor action
    */
    directFavor(netelement, event) {
      if (this.minified || ! netelement.hover) {
        return
      }

      event.preventDefault()
      this.favorNetelement(netelement)
    },
    favorNetelement(netelement) {
      this.loadingFavorites.push(netelement.id)

      axios({
        method: 'post',
        url: '/admin/Netelement/' + netelement.id + '/' + (this.favorites.includes(netelement.id) ? 'unfavorite' : 'favorite'),
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
      })
      .then(() => {
        this.loadingFavorites.splice(this.loadingFavorites.indexOf(netelement.id), 1)

        if (this.favorites.includes(netelement.id)) {
          this.netelements.splice(this.netelements.findIndex(n => !this.initialNE && n.id === netelement.id), 1)
          this.favorites.splice(this.favorites.indexOf(netelement.id), 1)

          if (this.activeNetelement = netelement.id) {
            localStorage.removeItem('sidebar-net')
            localStorage.removeItem('clicked-netelement')
          }

          return
        }

        if (this.netelements.findIndex(n => n.id == netelement.id) === -1) {
          this.netelements.push(netelement)
          this.netelements.sort((a, b) => a.id > b.id)
        }

        if (this.loadingFavorites.length === 0) {
          this.netelements = jQuery.extend(true, [], this.netelements)
        }

        this.favorites.push(netelement.id)
      })
      .catch((error) => {
          console.log(error)
          this.$snotify.error(error.message)
      })
    },
    setHover(netelement, state) {
      if (this.minified) {
        return
      }

      netelement.hover = state
      this.netelements = jQuery.extend(true, [], this.netelements)
    },
    netElementSearchHoverClass(netelement) {
      if (netelement.hover) {
        if (this.loadingFavorites.includes(netelement.id)) {
          return 'fa-circle-o-notch fa-spin'
        }

        return this.favorites.includes(netelement.id) ? 'fa-star range-handle' : 'fa-star-o range-handle'
      }

      return 'fa-sitemap'
    },
    setNetActive(id) {
      localStorage.setItem('clicked-netelement', id)
    }
  }
})

</script>
