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
require('./bootstrap')
require('./nmsprime')

import { createApp } from 'vue'
import snotify from 'vue3-snotify'

import app from './components/App.vue'
import sidebar from './components/Sidebar.vue'
import sidebarRight from './components/SidebarRight.vue'
import select2Component from './components/Select2.vue'
import skeletonComponent from './components/Skeleton.vue'
import overlay from "./components/Overlay.vue"
import HfcBaseMapActions from './components/HfcBase/HfcBaseMapActions.vue'
import HfcBaseHistoryTable from './components/HfcBase/HfcBaseHistoryTable.vue'
import HfcBaseHistorySlider from './components/HfcBase/HfcBaseHistorySlider.vue'
import Inventory from './components/inventory/Inventory.vue'

// dataTables
require('datatables.net-buttons/js/buttons.colVis.js')
require('datatables.net-buttons/js/buttons.html5.js')
require('datatables.net-buttons/js/buttons.print.js')

import pdfMake from 'pdfmake/build/pdfmake'
import pdfFonts from 'pdfmake/build/vfs_fonts'
import * as JSZip from 'jszip'
import 'datatables.net-select-bs4'
import 'datatables.net-buttons-bs4'
import 'datatables.net-responsive'

window.main = createApp(app)
  .component('overlay', overlay)
  .component('select2', select2Component)
  .component('skeleton', skeletonComponent)
  .use(snotify)
  .mount('#page-container')

window.navbar = createApp({}).mount('#header')

if (document.getElementById('sidebar')) {
  let propData = document.querySelector('#sidebar').dataset
  window.sidebar = createApp(sidebar, {
    favorites: JSON.parse(propData.favorites),
    netelements: JSON.parse(propData.netelements),
    netCount: propData.netCount
  })
    .component('select2', select2Component)
    .mount('#sidebar')

  pdfMake.vfs = pdfFonts.pdfMake.vfs
  window.JSZip = JSZip
}

if (document.getElementById('sidebar-right')) {
  window.sidebarRight = createApp(sidebarRight).mount('#sidebar-right')
}

if (document.getElementById('vue-body')) {
  window.real = createApp(Analysis).mount('#vue-body')
}

if (document.getElementById('map-actions')) {
  window.mapActions = createApp(HfcBaseMapActions).mount('#map-actions')
}

if (document.getElementById('HfcBase-history-table')) {
  window.hfcBaseHistoryTable = createApp(HfcBaseHistoryTable).use(snotify)
    .mount('#HfcBase-history-table')
}

if (document.getElementById('HfcBase-history-slider')) {
  window.hfcBaseHistorySlider = createApp(HfcBaseHistorySlider)
    .mount('#HfcBase-history-slider')
}

if (document.getElementById('inventoryApp')) {
	window.inventoryApp = createApp(Inventory).mount('#inventoryApp')
  }