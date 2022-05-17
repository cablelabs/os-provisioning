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

<script setup>
import { onMounted, ref } from 'vue'
import { store } from './../store/store'

const pinned = ref(false)

onMounted(() => {
  if (document.getElementById('sidebar-right')) {
    initSidebar()
  }
})

function initSidebar() {
  pinned.value = localStorage.getItem('pinned-right') === 'true'

  if (pinned.value) {
    store.minifiedRight = !pinned.value
  }
}

function pinSidebarRight() {
  pinned.value = !pinned.value
  localStorage.setItem('pinned-right', pinned.value)
}

function minifySidebarRight() {
  store.minifiedRight = !store.minifiedRight

  if (pinned.value) {
    pinSidebarRight()
  }
}
</script>
