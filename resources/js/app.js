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

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import './bootstrap'
import './nmsprime'

// dataTables
import 'datatables.net-buttons/js/buttons.colVis.js'
import 'datatables.net-buttons/js/buttons.html5.js'
import 'datatables.net-buttons/js/buttons.print.js'
import 'datatables.net-select-bs4'
import 'datatables.net-buttons-bs4'
import 'datatables.net-responsive'

// Panzoom
import Panzoom from '@panzoom/panzoom'
window.Panzoom = Panzoom

// Main App
import { createApp } from 'vue'
import snotify from 'vue3-snotify'

import app from './components/App.vue'
import sidebar from './components/Sidebar.vue'
import navbar from './components/Navbar.vue'
import popover from './components/Popover.vue'
import sidebarRight from './components/SidebarRight.vue'
import select2Component from './components/Select2.vue'
import SidebarSelect2Component from './components/SidebarSelect2.vue'
import skeletonComponent from './components/Skeleton.vue'
import overlay from './components/Overlay.vue'
import AuthAbilities from './components/AuthAbilities.vue'
import NavbarQuickviewNetwork from './components/navbar/QuickviewNetwork.vue'
import Collapse from './components/Collapse.vue'

// app
let appProps = document.querySelector('#page-container').dataset
window.main = createApp(app, {
    tabs: appProps.tabs ? JSON.parse(appProps.tabs) : [],
    defaultTab: appProps.defaultTab ? appProps.defaultTab : ''
  })
  .component('overlay', overlay)
  .component('select2', select2Component)
  .component('SidebarSelect2', SidebarSelect2Component)
  .component('skeleton', skeletonComponent)
  .component('popover', popover)
  .component('collapse', Collapse)
  .use(snotify)
  .mount('#page-container')

// navbar
window.navbar = createApp(navbar)
  .component('NavbarQuickviewNetwork', NavbarQuickviewNetwork)
  .component('select2', select2Component)
  .mount('#header')

// sidebar
if (document.getElementById('sidebar')) {
  let propData = document.querySelector('#sidebar').dataset
  window.sidebar = createApp(sidebar, {
    favorites: JSON.parse(propData.favorites),
    netelements: JSON.parse(propData.netelements),
    netCount: propData.netCount
  })
    .component('select2', select2Component)
    .component('SidebarSelect2', SidebarSelect2Component)
    .mount('#sidebar')
}

// right sidebar
if (document.getElementById('sidebar-right')) {
  window.sidebarRight = createApp(sidebarRight).mount('#sidebar-right')
}

if (document.getElementById('vue-body')) {
  window.real = createApp(Analysis).mount('#vue-body')
}

if (document.getElementById('auth-abilities')) {
  window.authAbilities = createApp(AuthAbilities)
    .component('popover', popover)
    .mount('#auth-abilities')
}

const themeColor = $('body').data('theme_color')

// as per browser preference theme color
if (themeColor === 'browser_preferences' && window.matchMedia && window.matchMedia("(prefers-color-scheme:dark)").matches) {
  document.write('<link rel="stylesheet" type="text/css" href="/components/assets-admin/css/config/dark_theme_config.css" />');
  $('body').addClass('dark');
} else if(themeColor === 'browser_preferences') {
  document.write('<link rel="stylesheet" type="text/css" href="/components/assets-admin/css/config/default_theme_config.css" />');
}
