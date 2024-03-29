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

import { reactive } from 'vue'

export const store = reactive({
  minified: localStorage.getItem('minified-state') === 'true',
  minifiedRight: true,
  hasSidebarRight: false,
  panelRightKeys: [],
  panelRightData: {},
  overlay: false,
  icons: {
    defaultColor: "#333",
    defaultSize: 20,
  },
  urlParams: Object.fromEntries(new URLSearchParams(window.location.search).entries()),
  snotify: null,
  hfcStorageRequest: {}
})
