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
import { createApp } from 'vue'
import snotify from 'vue3-snotify'

import ProvBaseConfigFileEdit from './components/ConfigFileEdit.vue'
import ModemAnalysis from './components/ModemAnalysis.vue'

if (document.getElementById('provbase-config-file-edit')) {
  window.provBaseConfigFileEdit = createApp(ProvBaseConfigFileEdit)
    .use(snotify)
    .directive('dispatchsel2', {
      inserted: function(e) {
        $(e).on('select2:select', function() {
          e.dispatchEvent(new Event('change'));
        });
        $(e).on('select2:unselect', function() {
          e.dispatchEvent(new Event('change'));
        });
      }
    })
    .mount('#provbase-config-file-edit')
}

 // prepare vue instance
if (document.getElementById('OpenSourceModemAnalysis')) {
  const propData = document.querySelector('#OpenSourceModemAnalysis').dataset
  let targetPage = window.location.href.split('?')[0]
  let panelPositionData = localStorage.getItem(targetPage) ? localStorage.getItem(targetPage) : localStorage.getItem(propData.viewHeader)
  let event = 'load'
  if (panelPositionData) {
    event = 'localstorage-position-loaded'
  }

  window.$(window).on(event, function() {
    window.$(document).ready(function() {
      window.modemAnalysis = createApp(ModemAnalysis)
        .mount('#OpenSourceModemAnalysis')
    })
  })
}
