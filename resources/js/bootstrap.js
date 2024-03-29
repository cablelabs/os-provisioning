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

// lodash
import isEmpty from 'lodash/isEmpty'
import round from 'lodash/round'

window._isEmpty = isEmpty;
window._round = round;

// jquery
import $ from 'jquery'
window.$ = window.jQuery = $

// jquery ui
import 'jquery-ui/ui/core'
import 'jquery-ui/ui/widgets/sortable'
import 'jquery-ui/ui/widgets/draggable'
import 'jquery-ui/ui/widgets/autocomplete'

// bootstrap
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// jQuery SlimScroll
import 'jquery-slimscroll/jquery.slimscroll.min.js';

// jstree
import 'jstree';

// ion-rangeslider
import 'ion-rangeslider';

// es6-promise
import 'es6-promise/auto';

// chartjs
import Chart from 'chart.js/auto'
window.Chart = Chart

// Pace
import Pace from 'pace-js'
window.Pace = Pace

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
let token = document.head.querySelector('meta[name="csrf-token"]')

if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
} else {
  console.error(
    'CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token'
  )
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher;
window.wssConnect = () => {
  window.echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: parseInt(process.env.MIX_WEBSOCKETS_PORT),
    wssPort: parseInt(process.env.MIX_WEBSOCKETS_PORT),
    forceTLS: process.env.MIX_PUSHER_FORCE_TLS === 'true',
    disableStats: true,
    enabledTransports: ['ws', 'wss']
  })
}
