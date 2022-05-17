/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap')
require('./nmsprime')

import { createApp } from 'vue'
import sidebar from './components/Sidebar.vue'
import sidebarRight from './components/SidebarRight.vue'
import app from './components/App.vue'
import snotify from 'vue3-snotify'
import 'vue3-snotify/style'
import select2Component from './components/Select2.vue'
import skeletonComponent from './components/Skeleton.vue'

window.main = createApp(app)
  .component('select2', select2Component)
  .component('skeleton', skeletonComponent)
  .use(snotify)
  .mount('#page-container')

window.navbar = createApp({}).mount('#header')

let propData = document.querySelector('#sidebar').dataset
window.sidebar = createApp(sidebar, {
  favorites: JSON.parse(propData.favorites),
  netelements: JSON.parse(propData.netelements),
  netCount: propData.netCount
})
  .component('select2', select2Component)
  .mount('#sidebar')

window.sidebarRight = createApp(sidebarRight)
  .mount('#sidebar-right')
