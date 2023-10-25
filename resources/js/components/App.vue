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
import { onMounted, ref, getCurrentInstance } from 'vue'
import { store } from '@/store/store'

export default {
  props: {
    tabs: [Object, Array],
    defaultTab: String
  },
  setup(props) {
    const tabStates = ref({})
    onMounted(() => {
      if (typeof Storage === 'undefined') {
        console.error('Sorry, no Web Storage Support - Cant save State of Sidebar - please update your Browser')
      }
      // prepare snotify
      window.snotify = getCurrentInstance().appContext.config.globalProperties.$snotify

      setupTabs()
    })

    function setupTabs() {
      if (! props.tabs.length) { // no edit page / no tabs
        return
      }

      props.tabs.map((tab) => tabStates.value[tab.name] = false)

      if (tabStates.value.hasOwnProperty(window.location.hash.substring(1))) {
        return setActiveTab(window.location.hash.substring(1))
      }

      if (props.defaultTab && tabStates.value.hasOwnProperty(props.defaultTab)) {
        return setActiveTab(props.defaultTab)
      }

      if (tabStates.value.hasOwnProperty('Edit')) {
        return setActiveTab('Edit')
      }

      // if everything fails, just set the second tab (first is guilog)
      // to be the active one
      if (props.tabs.length >= 2) {
        setActiveTab(props.tabs[1].name)
      }
    }

    const loggingTab = ref (false)
    function toggleLoggingTab(e) {
      loggingTab.value = !loggingTab.value
    }

    function setActiveTab(tabName) {
      Object.keys(tabStates.value).forEach(v => tabStates.value[v] = false)

      tabStates.value[tabName] = true
    }

    function setAndStoreActiveTab(tabName) {
      if (tabStates.value[tabName]) {
        return
      }

      setActiveTab(tabName)

      axios({
        method: 'post',
        url: '/admin/Session/SetActiveTab',
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        data: {
          tab: tabName,
          url: window.location.pathname,
        }
      })
    }

    function transformScroll(event) {
      if (!event.deltaY) {
        return;
      }

      event.currentTarget.scrollLeft += event.deltaY + event.deltaX;
      event.preventDefault();
    }

    return {
      store,
      loggingTab,
      toggleLoggingTab,
      tabStates,
      setActiveTab,
      setAndStoreActiveTab,
      transformScroll,
    }
  }
}
</script>
