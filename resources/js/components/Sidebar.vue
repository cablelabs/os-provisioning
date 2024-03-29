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

<script>
import { ref, onMounted, computed } from 'vue'
import { store } from '@/store/store'
import $ from 'jquery'

export default {
  props: {
    netelements: [Object, Array],
    favorites: [Object, Array],
    netCount: [Number, String]
  },
  setup(props) {
    onMounted(() => {
      netelements.value = props.netelements
      if (document.getElementById('sidebar')) {
        initSidebar()
        $(window).trigger('sidebar-loaded')
      }
    })

    const loopNetElements = computed(() => {
      if (isSearchMode.value) {
        return searchResults.value
      }

      return netelements.value
    })

    const menu = ref('Core Network')
    const leaveTimer = ref(null) // timer for leave and enter minified menu
    const showMinifiedHoverMenu = ref(false)
    const showMinifiedHoverNet = ref(false)
    const isVisible = ref(true) // show nets?
    const isSearchMode = ref(false)
    const initialNE = ref(true) // first 25 Nets - default
    const isCollapsed = ref(true) // show submenu: ul is extended (also for minified)
    const loadingClusters = ref([]) // loading circle for clusters
    const loadingFavorites = ref([]) // loading circle for favoriting
    const loadingSearch = ref(false) // loading circle for search
    const lastActive = ref('null')
    const lastClicked = ref('null')
    const activeItem = ref('null')
    const clickedItem = ref('null')
    const searchTimeout = ref(null) // debounce for search (500ms)
    const clusterSearch = ref('') // v-model for search
    const searchResults = ref([])
    const activeNetelement = ref(null)
    const clickedNetelement = ref(null)
    const netelements = ref([])
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || screen.width < 768

    function initSidebar() {
      initialNE.value = props.favorites.length === 0

      // load state of Net toggle
      isVisible.value = props.netCount ? localStorage.getItem('sidebar-net-isVisible') === 'true' : false
      isSearchMode.value = localStorage.getItem('sidebar-net-isSearchMode') === 'true'

      // load cached search term and results
      if (localStorage.getItem('sidebar-net-search') !== null) {
        clusterSearch.value = localStorage.getItem('sidebar-net-search')
      }

      if (localStorage.getItem('sidebar-net-searchResults') !== null) {
        searchResults.value = JSON.parse(localStorage.getItem('sidebar-net-searchResults'))
      }

      // init Sidebar active/clicked elements
      lastActive.value = activeItem.value = localStorage.getItem('sidebar-item')
      lastClicked.value = clickedItem.value = localStorage.getItem('clicked-item')
      activeNetelement.value = localStorage.getItem('sidebar-net')
      clickedNetelement.value = activeNetelement.value ? localStorage.getItem('clicked-netelement') : null

      // init collapse
      isCollapsed.value = false
      netelements.value.forEach((n) => {
        n.isCollapsed = true

        if (
          activeNetelement.value &&
          clickedNetelement.value &&
          parseInt(n.id) == parseInt(activeNetelement.value) &&
          !isSearchMode.value
        ) {
          loadCluster(n)
        }
      })

      if (isMobile) {
        store.minified = true
      }
    }

    function openSidebar(menuItem) {
      menu.value = menuItem

      if (store.minified) {
        handleMinify()
      }
    }

    window.addEventListener('sidebar.toggle', function (e) {
      handleMinify()
    });

    /**
     * Handles the Click on the minify button. A lot of the logic copied from
     * color admin due to conflicts.
     */
    function handleMinify() {
      if (store.minified || !isMobile) {
        $(document.getElementById('sidebar')).slimScroll({ destroy: true })
      }

      isCollapsed.value = true
      netelements.value.forEach((n) => (n.isCollapsed = true))

      if (isVisible.value && isSearchMode.value) {
        setSearchMode()
      }

      store.minified = !store.minified

      if (isMobile) {
        return
      }

      pushPinnedStateToSession(!store.minified) // pinned is opposite of minified
      localStorage.setItem('minified-state', store.minified)

      setTimeout(() => {
        $(window).trigger('resize')
        if ($('table.datatable').length && !store.minified) {
          // resize datatables after animation
          $('table.datatable').DataTable().responsive.recalc()
        }
      }, 200)
    }

    function pushPinnedStateToSession(state) {
      axios({
          method: 'post',
          url: '/admin/Sidebar/setPinnedState',
          contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
          data: {
            pinned: state
          }
        })
          .then((response) => {
            snotify.success(response.data.message)
          })
          .catch((error) => {
            console.error(error)
            snotify.error(error.message)
          })
    }

    /**
     * Handles the mouse leave of the minified menu for net and main menu.
     */
    function leaveMinifiedSidebar(netelement = 'null') {
      if (netelement !== 'null') {
        return (leaveTimer.value = setTimeout(() => {
          netelements.value.forEach((n) => (n.isCollapsed = true))
          showMinifiedHoverNet.value = false
        }, 250))
      }

      leaveTimer.value = setTimeout(() => {
        showMinifiedHoverMenu.value = false
        isCollapsed.value = true
      }, 250)
    }

    /**
     * Collapse all nets except the selected one. assuers that only one submenu
     * is visible to the user.
     */
    function toggleNetMinified(netelement) {
      clearTimeout(leaveTimer.value)
      netelements.value.forEach((n) => (n.isCollapsed = n.id !== netelement.id))
      showMinifiedHoverNet.value = true
    }

    /**
     * Handles the enter and leave of the minified net menu
     */
    function minifiedSidebarNet(netelement, type) {
      if (!store.minified) {
        return
      }

      if (type == 'enter') {
        showMinifiedHoverNet.value = true
        netelement.isCollapsed = false
        return clearTimeout(leaveTimer.value)
      }

      leaveMinifiedSidebar(netelement)
    }

    function setVisibility() {
      isVisible.value = !isVisible.value

      localStorage.setItem('sidebar-net-isVisible', isVisible.value)
    }

    function setSearchMode() {
      isSearchMode.value = !isSearchMode.value

      localStorage.setItem('sidebar-net-isSearchMode', isSearchMode.value)
    }

    /**
     * Handles the Collapse and expand logic of the main menu (also for minified)
     */
    function setMenu(name, collapse = true) {
      if (name === activeItem.value && !store.minified && collapse) {
        return (isCollapsed.value = !isCollapsed.value)
      }

      activeItem.value = name
      clickedItem.value = name
      isCollapsed.value = !collapse

      if (store.minified) {
        clearTimeout(leaveTimer.value)
      }

      localStorage.setItem('sidebar-item', name)
      localStorage.setItem('clicked-item', name)
    }

    function setSubMenu(name) {
      clickedItem.value = name

      localStorage.setItem('clicked-item', name)
    }

    function showSubMenu(name, minified = false) {
      if (!minified) {
        return activeItem.value == name && !isCollapsed.value && !store.minified
      }

      return activeItem.value == name && !isCollapsed.value
    }

    /**
     * The following methods handle the accordion animation
     */
    function beforeEnter(el) {
      if (el.style.maxHeight) {
        el.style.maxHeight = '0'
      }
    }

    function enter(el) {
      if (el.style.maxHeight) {
        el.style.maxHeight = el.scrollHeight + 'px'
      }
    }

    function beforeLeave(el) {
      el.style.maxHeight = el.scrollHeight + 'px'
    }

    function leave(el) {
      el.style.maxHeight = '0'
    }

    function afterLeave(el) {
      el.style.maxHeight = 1000 + 'px'
    }

    /**
     * Ajax Request for Search from Input. (isSearchMode: true)
     */
    function searchForNetOrCluster(event) {
      clearTimeout(searchTimeout.value)
      loadingSearch.value = true
      localStorage.setItem('sidebar-net-search', clusterSearch.value)

      searchTimeout.value = setTimeout(() => {
        if (clusterSearch.value === '') {
          return
        }

        axios({
          method: 'post',
          url: '/admin/Netelement/netclustersearch',
          contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
          data: {
            query: clusterSearch.value
          }
        })
          .then((response) => {
            searchResults.value = response.data
            searchResults.value.forEach((n) => (n.isCollapsed = true))

            loadingSearch.value = false
            localStorage.setItem('sidebar-net-searchResults', JSON.stringify(response.data))
          })
          .catch((error) => {
            console.error(error)
            snotify.error(error.message)
          })
      }, 500)
    }

    /**
     * Lazy load Clusters via AJAX and also handles expand/collapse Logic
     */
    function loadCluster(netelement) {
      netelement.isCollapsed = !netelement.isCollapsed

      if (store.minified) {
        toggleNetMinified(netelement)
      }

      localStorage.setItem('sidebar-net', netelement.id)
      setNetActive(netelement.id)
      if (netelement.isCollapsed) {
        localStorage.removeItem('sidebar-net')
        removeNetActive(netelement.id)
      }

      if (netelement.clustersLoaded) {
        return
      }

      loadingClusters.value.push(netelement.id)

      axios({
        method: 'post',
        url: '/admin/Netelement/' + netelement.id + '/clustersearch',
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
      })
        .then((response) => {
          netelement.clustersLoaded = true
          loadingClusters.value.splice(loadingClusters.value.indexOf(netelement.id), 1)
          if (isSearchMode.value) {
            searchResults.value[searchResults.value.findIndex((n) => n.id === netelement.id)].clusters = response.data
            searchResults.value = jQuery.extend(true, [], searchResults.value) // Object deep Copy is necessary to detect changes
            return localStorage.setItem('sidebar-net-searchResults', JSON.stringify(searchResults.value))
          }

          netelements.value[netelements.value.findIndex((n) => n.id === netelement.id)].clusters = response.data

          if (loadingClusters.value.length === 0) {
            netelements.value = jQuery.extend(true, [], netelements.value)
          }
        })
        .catch((error) => {
          console.error(error)
          snotify.error(error.message)
        })
    }

    /**
     * Helper function to simplify favor action
     */
    function directFavor(netelement, event) {
      if (store.minified || !netelement.hover) {
        return
      }

      event.preventDefault()
      favorNetelement(netelement)
    }

    function favorNetelement(netelement) {
      loadingFavorites.value.push(netelement.id)

      axios({
        method: 'post',
        url:
          '/admin/Netelement/' +
          netelement.id +
          '/' +
          (props.favorites.includes(netelement.id) ? 'unfavorite' : 'favorite'),
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
      })
        .then(() => {
          loadingFavorites.value.splice(loadingFavorites.value.indexOf(netelement.id), 1)

          if (props.favorites.includes(netelement.id)) {
            netelements.value.splice(
              netelements.value.findIndex((n) => !initialNE.value && n.id === netelement.id),
              1
            )
            props.favorites.splice(props.favorites.indexOf(netelement.id), 1)

            if (activeNetelement.value == netelement.id) {
              localStorage.removeItem('sidebar-net')
              localStorage.removeItem('clicked-netelement')
            }

            return
          }

          if (netelements.value.findIndex((n) => n.id == netelement.id) === -1) {
            netelements.value.push(netelement)
            netelements.value.sort((a, b) => a.id > b.id)
          }

          if (loadingFavorites.value.length === 0) {
            netelements.value = jQuery.extend(true, [], netelements.value)
          }

          props.favorites.push(netelement.id)
        })
        .catch((error) => {
          console.error(error)
          snotify.error(error.message)
        })
    }

    function setHover(netelement, state) {
      if (store.minified) {
        return
      }

      netelement.hover = state
      netelements.value = jQuery.extend(true, [], netelements.value)
    }

    function netElementSearchHoverClass(netelement) {
      if (netelement.hover) {
        if (loadingFavorites.value.includes(netelement.id)) {
          return 'fa-circle-o-notch fa-spin'
        }

        return props.favorites.includes(netelement.id) ? 'fa-star range-handle' : 'fa-star-o range-handle'
      }

      return 'fa-sitemap'
    }

    function setNetActive(id) {
      localStorage.setItem('clicked-netelement', id)
    }

    function removeNetActive(id) {
      localStorage.removeItem('clicked-netelement', id)
    }

    const network = ref(0)
    const market = ref(0)
    const hubsite = ref(0)
    const ccap = ref(0)
    const dpa = ref(0)
    const ncs = ref(0)
    const rpa = ref(0)
    const rpd = ref(0)
    const cpe = ref(0)

    function route(id, route) {
      if (!id) {
        return '#'
      }

      return route.replace('NETELEMENT_ID', id)
    }

    function ajaxRoute(route) {
      let url = new URL(route)

      url.searchParams.set('network', network.value)
      url.searchParams.set('market', market.value)
      url.searchParams.set('hubsite', hubsite.value)
      url.searchParams.set('ccap', ccap.value)
      url.searchParams.set('dpa', dpa.value)
      url.searchParams.set('ncs', ncs.value)
      url.searchParams.set('rpa', rpa.value)
      url.searchParams.set('rpd', rpd.value)
      url.searchParams.set('cpe', cpe.value)

      return url.toString()
    }

    function updateref(payload) {
      switch (payload.ref) {
        case 'network':
          network.value = payload.value
          break
        case 'market':
          market.value = payload.value
          break
        case 'hubsite':
          hubsite.value = payload.value
          break
        case 'ccap':
          ccap.value = payload.value
          break
        case 'dpa':
          dpa.value = payload.value
          break
        case 'ncs':
          ncs.value = payload.value
          break
        case 'rpa':
          rpa.value = payload.value
          break
        case 'rpd':
          rpd.value = payload.value
          break
        case 'cpe':
          cpe.value = payload.value
          break
      }
    }

    return {
      menu,
      leaveTimer,
      showMinifiedHoverMenu,
      showMinifiedHoverNet,
      isVisible,
      isSearchMode,
      initialNE,
      isCollapsed,
      loadingClusters,
      loadingFavorites,
      loadingSearch,
      lastActive,
      lastClicked,
      activeItem,
      clickedItem,
      searchTimeout,
      clusterSearch,
      searchResults,
      activeNetelement,
      clickedNetelement,
      netelements,
      network,
      market,
      hubsite,
      ccap,
      dpa,
      ncs,
      rpa,
      rpd,
      cpe,
      updateref,
      ajaxRoute,
      route,
      removeNetActive,
      setNetActive,
      netElementSearchHoverClass,
      setHover,
      favorNetelement,
      directFavor,
      loadCluster,
      searchForNetOrCluster,
      afterLeave,
      leave,
      beforeLeave,
      enter,
      beforeEnter,
      showSubMenu,
      setSubMenu,
      setMenu,
      setSearchMode,
      setVisibility,
      minifiedSidebarNet,
      toggleNetMinified,
      leaveMinifiedSidebar,
      handleMinify,
      openSidebar,
      initSidebar,
      loopNetElements,
      store,
    }
  }
}
</script>
