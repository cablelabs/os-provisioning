<script setup>
import { ref, onMounted, computed } from 'vue'
import { store } from './../store/store'

onMounted(() => {
  if (document.getElementById('sidebar')) {
    initSidebar()
  }
})

const props = defineProps({
  netelements: [Object, Array],
  favorites: [Object, Array],
  netCount: Number
})

const loopNetElements = computed(() => {
  if (isSearchMode.value) {
    return searchResults.value
  }

  return props.netelements
})

const menu = ref('Core Network')
const pinned = ref(false)
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

function initSidebar() {
  initialNE.value = props.favorites.length === 0
  pinned.value = localStorage.getItem('core-network-pinned') === 'true'

  // load minified state
  if (pinned.value) {
    store.minified = localStorage.getItem('minified-state') === 'true'
  }

  // load state of Net toggle
  isVisible.value = props.netCount
    ? localStorage.getItem('sidebar-net-isVisible') === 'true'
    : false
  isSearchMode.value =
    localStorage.getItem('sidebar-net-isSearchMode') === 'true'

  // load cached search term and results
  if (localStorage.getItem('sidebar-net-search') !== null) {
    clusterSearch.value = localStorage.getItem('sidebar-net-search')
  }

  if (localStorage.getItem('sidebar-net-searchResults') !== null) {
    searchResults.value = JSON.parse(
      localStorage.getItem('sidebar-net-searchResults')
    )
  }

  // init Sidebar active/clicked elements
  lastActive.value = activeItem.value = localStorage.getItem('sidebar-item')
  lastClicked.value = clickedItem.value = localStorage.getItem('clicked-item')
  activeNetelement.value = localStorage.getItem('sidebar-net')
  clickedNetelement.value = activeNetelement.value
    ? localStorage.getItem('clicked-netelement')
    : null

  // init collapse
  isCollapsed.value = false
  props.netelements.forEach((n) => {
    n.isCollapsed = true

    if (
      activeNetelement.value &&
      clickedNetelement.value &&
      n.id == activeNetelement.value &&
      !isSearchMode.value
    ) {
      loadCluster(n)
    }
  })
}

function pinSidebar() {
  if (!store.minified && !pinned.value) {
    localStorage.setItem('core-network-pinned', true)
    return (pinned.value = true)
  }

  localStorage.setItem('core-network-pinned', false)
  pinned.value = false
}

function openSidebar(menuItem) {
  menu.value = menuItem

  if (store.minified) {
    handleMinify()
  }
}

/**
 * Handles the Click on the minify button. A lot of the logic copied from
 * color admin due to conflicts.
 */
function handleMinify() {
  let sidebar = document.getElementById('sidebar')

  sidebar.style.marginTop = 0
  sidebar.style.overflow = 'visible'
  sidebar.removeAttribute('data-init')

  if (!store.minified) {
    pinSidebar()
  }

  if (store.minified || !/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    $(sidebar).slimScroll({ destroy: true })
  }

  sidebar.style.transition = 'all .15s ease-in-out'
  store.minified = !store.minified
  isCollapsed.value = true
  props.netelements.forEach((n) => (n.isCollapsed = true))

  if (isVisible.value && isSearchMode.value) {
    setSearchMode()
  }

  localStorage.setItem('minified-state', store.minified)
  setTimeout(() => {
    $(window).trigger('resize')
    if ($('table.datatable').length && !store.minified) {
      // resize datatables after animation
      $('table.datatable').DataTable().responsive.recalc()
    }
  }, 200)
}

/**
 * Handles the mouse leave of the minified menu for net and main menu.
 */
function leaveMinifiedSidebar(netelement = 'null') {
  if (netelement !== 'null') {
    return (leaveTimer.value = setTimeout(() => {
      props.netelements.forEach((n) => (n.isCollapsed = true))
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
  props.netelements.forEach((n) => (n.isCollapsed = n.id !== netelement.id))
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
  el.style.maxHeight = '0'
}

function enter(el) {
  el.style.maxHeight = el.scrollHeight + 'px'
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
        localStorage.setItem(
          'sidebar-net-searchResults',
          JSON.stringify(response.data)
        )
      })
      .catch((error) => {
        console.error(error)
        main.$snotify.error(error.message)
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
  if (netelement.isCollapsed) {
    localStorage.removeItem('sidebar-net')
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
      loadingClusters.value.splice(
        loadingClusters.value.indexOf(netelement.id),
        1
      )
      if (isSearchMode.value) {
        searchResults.value[
          searchResults.value.findIndex((n) => n.id === netelement.id)
        ].clusters = response.data
        searchResults.value = jQuery.extend(true, [], searchResults.value) // Object deep Copy is necessary to detect changes
        return localStorage.setItem(
          'sidebar-net-searchResults',
          JSON.stringify(searchResults.value)
        )
      }

      props.netelements[
        props.netelements.findIndex((n) => n.id === netelement.id)
      ].clusters = response.data

      if (loadingClusters.value.length === 0) {
        props.netelements = jQuery.extend(true, [], props.netelements)
      }
    })
    .catch((error) => {
      console.error(error)
      main.$snotify.error(error.message)
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
      loadingFavorites.value.splice(
        loadingFavorites.value.indexOf(netelement.id),
        1
      )

      if (props.favorites.includes(netelement.id)) {
        props.netelements.splice(
          props.netelements.findIndex(
            (n) => !initialNE.value && n.id === netelement.id
          ),
          1
        )
        props.favorites.splice(props.favorites.indexOf(netelement.id), 1)

        if (activeNetelement.value == netelement.id) {
          localStorage.removeItem('sidebar-net')
          localStorage.removeItem('clicked-netelement')
        }

        return
      }

      if (props.netelements.findIndex((n) => n.id == netelement.id) === -1) {
        props.netelements.push(netelement)
        props.netelements.sort((a, b) => a.id > b.id)
      }

      if (loadingFavorites.value.length === 0) {
        props.netelements = jQuery.extend(true, [], props.netelements)
      }

      props.favorites.push(netelement.id)
    })
    .catch((error) => {
      console.error(error)
      main.$snotify.error(error.message)
    })
}

function setHover(netelement, state) {
  if (store.minified) {
    return
  }

  netelement.hover = state
  props.netelements = jQuery.extend(true, [], props.netelements)
}

function netElementSearchHoverClass(netelement) {
  if (netelement.hover) {
    if (loadingFavorites.value.includes(netelement.id)) {
      return 'fa-circle-o-notch fa-spin'
    }

    return props.favorites.includes(netelement.id)
      ? 'fa-star range-handle'
      : 'fa-star-o range-handle'
  }

  return 'fa-sitemap'
}

function setNetActive(id) {
  localStorage.setItem('clicked-netelement', id)
}

const network = ref(0)
const market = ref(0)
const hub = ref(0)
const ccap = ref(0)
const leaf = ref(0)
const spine = ref(0)
const node = ref(0)
const rpd = ref(0)
const cm = ref(0)

function route(id, route) {
  if (!id) {
    return '#';
  }

  return route.replace('NETELEMENT_ID', id)
}

function ajaxRoute(route) {
  let url = new URL(route)

  url.searchParams.set('network', network.value)
  url.searchParams.set('market', market.value)
  url.searchParams.set('hub', hub.value)
  url.searchParams.set('ccap', ccap.value)
  url.searchParams.set('leaf', leaf.value)
  url.searchParams.set('spine', spine.value)
  url.searchParams.set('node', node.value)
  url.searchParams.set('spine', spine.value)
  url.searchParams.set('rpd', rpd.value)
  url.searchParams.set('cm', cm.value)

  return url.toString()
}
</script>
