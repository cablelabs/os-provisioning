<script setup>
import { ref, onMounted, computed, getCurrentInstance } from 'vue'

// prepare default vaules
let propData = document.querySelector('#ticketsystem-receiver').dataset

// refs
const refs = ref(null)
const isMobile = ref(false)
const autorefresh = ref(true)
const ticketId = ref(null)
const permission = ref(null)
const refreshTimer = ref(null)
const serviceWorker = ref(null)
const loadingComments = ref(false)
const showComments = ref(false)
const commentMessage = ref('')
const comments = ref({})
const commentRefreshTimer = ref(null)
const showNotificationModal = ref(false)
const transitionEndEventName = ref(null)
const states = ref([])
const tickets = ref([])
const notifications = ref([])
const priorities = ref([])
const lifetime = ref('')
const ticketState = ref(null)

// mounted
onMounted(async () => {
  refs.value = getCurrentInstance().ctx.$refs

  await init()

  transitionEndEventName.value = getTransitionEndEventName()

  if (storageAvailable('localStorage') && localStorage.getItem('ticketreceiver.update')) {
    autorefresh.value = localStorage.getItem('ticketreceiver.update') === "true"
  }

  if (autorefresh.value) {
    startRefreshTimer()
  }

  if (window.Notification && Notification.permission !== "granted") {
    showNotificationModal.value = true
  } else {
    try {
    await requestNotificationPermission()
    await registerServiceWorker()
    } catch (error) {
    console.error(error)
    }
  }

  detectMobile()
  document.getElementById('ticketsystem-receiver').style.display = 'flex';
  document.getElementById('load').style.display = 'none';
})

// computed
const onlyClosedTickets = computed(() => {
  return Object.keys(tickets.value).length === 1 && tickets.value.hasOwnProperty(ticketState.value)
})

// methods
async function init() {
  const res = await axios.get('/admin/api/v1/TicketReceiver');
  if (res.data.success) {
    states.value = res.data.states
    tickets.value = res.data.tickets.tickets
    notifications.value = res.data.tickets.notifications
    priorities.value = res.data.priorities
    lifetime.value = res.data.lifeTime
    ticketState.value = res.data.ticketState
  }
  return
}

function escape(text) {
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function detectMobile() {
  isMobile.value = false //initiate as false
  // device detection
  if ((new RegExp(propData.isMobileRegEx1).test(navigator.userAgent) ||
    new RegExp(propData.isMobileRegEx2).test(navigator.userAgent.substr(0,4))) &&
    window.matchMedia("only screen and (max-width: 991px)").matches) {
    isMobile.value = true
  }
}

function toggleInfo(id) {
  if (ticketId.value) {
  document.body.style.overflow = 'auto'
  return ticketId.value = null
  }

  ticketId.value = id
  document.body.style.overflow = 'hidden'
}

function swipeOnInfo(direction, event) {
  if (!isMobile.value) {
    return
  }

  if (direction == 'right') {
    toggleInfo(ticketId.value)
  }

  if (direction == 'left') {
    openComments()
  }
}

function swipeOnComments(direction, event) {
  if (!isMobile.value) {
    return
  }

  if (direction == 'right') {
    hideComments()
  }
}

function handleCommentInput(event) {
  commentMessage.value = event.target.innerText.replace(/\n{2,}/g, '\n')
}

function openComments() {
  showComments.value = true
  comments.value = {}
  loadingComments.value = true
  loadComments(true)

  commentRefreshTimer.value = setInterval(() => {
    loadComments()
  }, lifetime.value)
}

function hideComments() {
  showComments.value = false
  clearInterval(commentRefreshTimer.value)
}

function loadComments(initial = false) {
  axios({                                                                                      
    method: 'get',
    url: '/admin/api/v1/Comment',
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    headers: {'Authorization': `Bearer ${propData.apiToken}`},
    params: {
      filter_groups: [{
        filters: [{
          key: 'ticket_id',
          value: this.ticketId,
          operator: 'eq',
          not: false
        }], or: false
      }], includes: ['user'], sort: [{key: 'created_at', direction: 'asc'}]
    }
  })
  .then(response => {
    comments.value = response.data
    loadingComments.value = false
  })
  .then(() => {
    if (initial && Object.keys(comments.value).length) {
      refs.value.commentContainer[0].scrollTop = refs.value.commentContainer[0].scrollHeight
    }
  })
  .catch(error => {
    loadingComments.value = false
    console.error('Error:', error)
    $snotify.error(error, 'Error:')
  })
}
function saveComment() {
  if (commentMessage.value.trim() === '') {
    return;
  }

  axios({
    method: 'post',
    url: '/admin/api/v1/Comment',
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    headers: {'Authorization': `Bearer ${propData.apiToken}`},
    data: {ticket_id: ticketId.value, comment: commentMessage.value, user_id: propData.userId }
  })
  .then(response => {
    loadComments(true);
    commentMessage.value = '';
  })
  .catch(error => {
    loadingComments.value = false
    console.error('Error:', error)
    this.$snotify.error(error, 'Error:')
  });
}
function showTicketInfo(element) {
  setTimeout(()=> {
    element.style.left = '0vw'
  }, 50)
}
function hideTicketInfo(element) {
  element.style.left = '100vw'
}
function getTransitionEndEventName() {
  let transitions = {
    "transition"    : "transitionend",
    "OTransition"   : "oTransitionEnd",
    "MozTransition"   : "transitionend",
    "WebkitTransition": "webkitTransitionEnd"
  }
  let bodyStyle = document.body.style

  for(let transition in transitions) {
    if(bodyStyle[transition] != undefined) {
      return transitions[transition]
    }
  }
}
function storageAvailable(type) {
  let storage;
  try {
    storage = window[type];
    let x = '__storage_test__';
    storage.setItem(x, x);
    storage.removeItem(x);
    return true;
  }
  catch(e) {
    return e instanceof DOMException && (
      // everything except Firefox
      e.code === 22 ||
      // Firefox
      e.code === 1014 ||
      // test name field too, because code might not be present
      // everything except Firefox
      e.name === 'QuotaExceededError' ||
      // Firefox
      e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
      // acknowledge QuotaExceededError only if there's something already stored
      (storage && storage.length !== 0);
  }
}
async function requestNotificationPermission() {
  showNotificationModal.value = false
  permission.value = await window.Notification.requestPermission()

  if(permission.value !== 'granted'){
    throw new Error('Permission not granted for Notifications! You will not get Updates!')
  }
}
async function registerServiceWorker() {
  return serviceWorker.value = await navigator.serviceWorker.register('/components/service-worker.js')
}
function touch(event, offset) {
  if (!isMobile.value) {
    return
  }

  let path = event.composedPath()
  let linkElement = Object.keys(path).find(key => path[key].tagName == 'A')
  let foreground = path[linkElement].children[1].children[1]
  foreground.style.strokeDashoffset = offset;
}
function touchStart(event) {
  touch(event, 0)
}
function touchEnd(event) {
  touch(event, 408)
}
function startRefreshTimer() { // will be removed when broadcasting is enabled system-wide
  if (storageAvailable('localStorage')) {
    localStorage.setItem('ticketreceiver.update', true);
  }

  autorefresh.value = true
  refreshTimer.value = setInterval(() => {
    refreshTickets()
  }, lifetime.value)
}
function stopRefreshTimer() {
  if (storageAvailable('localStorage')) {
    localStorage.setItem('ticketreceiver.update', false);
  }

  autorefresh.value = false
  clearInterval(refreshTimer.value)
}
function toggleTimeout() {
  if (autorefresh.value) {
    return stopRefreshTimer()
  }

  startRefreshTimer()
}
function showNotification(title, body, img = null) {
  const options = {
    body: body,
    icon: img ? img : 'images/help.png',
    badge: img ? img : 'images/help.png',
    vibrate: 100
  }

  $snotify.success(body, title)
  serviceWorker.value.showNotification(title, options);
}
function refreshTickets() {
  fetch(propData.ticketReceiverRefreshRoute, {
    method: 'get',
    headers: {
    'X-CSRF-TOKEN': propData.csrfToken,
    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
    }
  })
  .then(response => response.json())
  .then(data => {
    handleRefreshData(data)
  })
  .catch((error) => {
    console.error('Error:', error)
    $snotify.error(error, 'Error:')
  })
}
function submit(event) {
  if (isMobile.value && (event.target.style.strokeDashoffset === '408' || event.target.style.strokeDashoffset === '408px')) {
    return
  }

  clearInterval(refreshTimer.value)
  let path = event.composedPath()
  let linkElement = Object.keys(path).find(key => path[key].tagName == 'A')
  navigator.vibrate(200)

  fetch(path[linkElement].href, {
    method: 'get',
    headers: {
      'X-CSRF-TOKEN': propData.csrfToken,
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
    }
  })
  .then(response => response.json())
  .then(data => {
    clearInterval(refreshTimer.value)
    handleRefreshData(data)
  })
  .catch((error) => {
    console.error('Error:', error)
    $snotify.error(error, 'Error:')
  })

  toggleInfo(ticketId.value)
}
function handleRefreshData(data) {
  tickets.value = data.tickets

  if (data.notifications.length != notifications.value.length) {
    refreshNavbarNotifications();
  }

  if (data.notifications.length > notifications.value.length) {
    for (notification of data.notifications.slice(0 ,data.notifications.length - notifications.value.length)) {
      showNotification(
        notification.data.name+': '+notification.data.title,
        notification.data.user,
        notification.data.imgPath ? notification.data.imgPath : null
      )
    }
  }

  notifications.value = data.notifications
}
function refreshNavbarNotifications() {
  fetch(propData.notificationsNavbarRoute, {
    method: 'get',
    headers: {
      'X-CSRF-TOKEN': propData.csrfToken,
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
    }
  })
  .then(response => response.text())
  .then(html => {
    document.getElementById('js-notifications').outerHTML = html
  })
  .catch((error) => {
    console.error('Error:', error)
    $snotify.error(error, 'Error:')
  })
}
</script>
<style>
.chatbox {
  background-color: #e6e6e6;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 304 304' width='304' height='304'%3E%3Cpath fill='%238ec73a' fill-opacity='0.1' d='M44.1 224a5 5 0 1 1 0 2H0v-2h44.1zm160 48a5 5 0 1 1 0 2H82v-2h122.1zm57.8-46a5 5 0 1 1 0-2H304v2h-42.1zm0 16a5 5 0 1 1 0-2H304v2h-42.1zm6.2-114a5 5 0 1 1 0 2h-86.2a5 5 0 1 1 0-2h86.2zm-256-48a5 5 0 1 1 0 2H0v-2h12.1zm185.8 34a5 5 0 1 1 0-2h86.2a5 5 0 1 1 0 2h-86.2zM258 12.1a5 5 0 1 1-2 0V0h2v12.1zm-64 208a5 5 0 1 1-2 0v-54.2a5 5 0 1 1 2 0v54.2zm48-198.2V80h62v2h-64V21.9a5 5 0 1 1 2 0zm16 16V64h46v2h-48V37.9a5 5 0 1 1 2 0zm-128 96V208h16v12.1a5 5 0 1 1-2 0V210h-16v-76.1a5 5 0 1 1 2 0zm-5.9-21.9a5 5 0 1 1 0 2H114v48H85.9a5 5 0 1 1 0-2H112v-48h12.1zm-6.2 130a5 5 0 1 1 0-2H176v-74.1a5 5 0 1 1 2 0V242h-60.1zm-16-64a5 5 0 1 1 0-2H114v48h10.1a5 5 0 1 1 0 2H112v-48h-10.1zM66 284.1a5 5 0 1 1-2 0V274H50v30h-2v-32h18v12.1zM236.1 176a5 5 0 1 1 0 2H226v94h48v32h-2v-30h-48v-98h12.1zm25.8-30a5 5 0 1 1 0-2H274v44.1a5 5 0 1 1-2 0V146h-10.1zm-64 96a5 5 0 1 1 0-2H208v-80h16v-14h-42.1a5 5 0 1 1 0-2H226v18h-16v80h-12.1zm86.2-210a5 5 0 1 1 0 2H272V0h2v32h10.1zM98 101.9V146H53.9a5 5 0 1 1 0-2H96v-42.1a5 5 0 1 1 2 0zM53.9 34a5 5 0 1 1 0-2H80V0h2v34H53.9zm60.1 3.9V66H82v64H69.9a5 5 0 1 1 0-2H80V64h32V37.9a5 5 0 1 1 2 0zM101.9 82a5 5 0 1 1 0-2H128V37.9a5 5 0 1 1 2 0V82h-28.1zm16-64a5 5 0 1 1 0-2H146v44.1a5 5 0 1 1-2 0V18h-26.1zm102.2 270a5 5 0 1 1 0 2H98v14h-2v-16h124.1zM242 149.9V160h16v34h-16v62h48v48h-2v-46h-48v-66h16v-30h-16v-12.1a5 5 0 1 1 2 0zM53.9 18a5 5 0 1 1 0-2H64V2H48V0h18v18H53.9zm112 32a5 5 0 1 1 0-2H192V0h50v2h-48v48h-28.1zm-48-48a5 5 0 0 1-9.8-2h2.07a3 3 0 1 0 5.66 0H178v34h-18V21.9a5 5 0 1 1 2 0V32h14V2h-58.1zm0 96a5 5 0 1 1 0-2H137l32-32h39V21.9a5 5 0 1 1 2 0V66h-40.17l-32 32H117.9zm28.1 90.1a5 5 0 1 1-2 0v-76.51L175.59 80H224V21.9a5 5 0 1 1 2 0V82h-49.59L146 112.41v75.69zm16 32a5 5 0 1 1-2 0v-99.51L184.59 96H300.1a5 5 0 0 1 3.9-3.9v2.07a3 3 0 0 0 0 5.66v2.07a5 5 0 0 1-3.9-3.9H185.41L162 121.41v98.69zm-144-64a5 5 0 1 1-2 0v-3.51l48-48V48h32V0h2v50H66v55.41l-48 48v2.69zM50 53.9v43.51l-48 48V208h26.1a5 5 0 1 1 0 2H0v-65.41l48-48V53.9a5 5 0 1 1 2 0zm-16 16V89.41l-34 34v-2.82l32-32V69.9a5 5 0 1 1 2 0zM12.1 32a5 5 0 1 1 0 2H9.41L0 43.41V40.6L8.59 32h3.51zm265.8 18a5 5 0 1 1 0-2h18.69l7.41-7.41v2.82L297.41 50H277.9zm-16 160a5 5 0 1 1 0-2H288v-71.41l16-16v2.82l-14 14V210h-28.1zm-208 32a5 5 0 1 1 0-2H64v-22.59L40.59 194H21.9a5 5 0 1 1 0-2H41.41L66 216.59V242H53.9zm150.2 14a5 5 0 1 1 0 2H96v-56.6L56.6 162H37.9a5 5 0 1 1 0-2h19.5L98 200.6V256h106.1zm-150.2 2a5 5 0 1 1 0-2H80v-46.59L48.59 178H21.9a5 5 0 1 1 0-2H49.41L82 208.59V258H53.9zM34 39.8v1.61L9.41 66H0v-2h8.59L32 40.59V0h2v39.8zM2 300.1a5 5 0 0 1 3.9 3.9H3.83A3 3 0 0 0 0 302.17V256h18v48h-2v-46H2v42.1zM34 241v63h-2v-62H0v-2h34v1zM17 18H0v-2h16V0h2v18h-1zm273-2h14v2h-16V0h2v16zm-32 273v15h-2v-14h-14v14h-2v-16h18v1zM0 92.1A5.02 5.02 0 0 1 6 97a5 5 0 0 1-6 4.9v-2.07a3 3 0 1 0 0-5.66V92.1zM80 272h2v32h-2v-32zm37.9 32h-2.07a3 3 0 0 0-5.66 0h-2.07a5 5 0 0 1 9.8 0zM5.9 0A5.02 5.02 0 0 1 0 5.9V3.83A3 3 0 0 0 3.83 0H5.9zm294.2 0h2.07A3 3 0 0 0 304 3.83V5.9a5 5 0 0 1-3.9-5.9zm3.9 300.1v2.07a3 3 0 0 0-1.83 1.83h-2.07a5 5 0 0 1 3.9-3.9zM97 100a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-48 32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm32 48a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm32-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm32 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16-64a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 96a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-144a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-96 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm96 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16-64a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-32 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM49 36a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-32 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm32 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM33 68a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-48a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 240a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16-64a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16-32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm80-176a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm32 48a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm112 176a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM17 180a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM17 84a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm32 64a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6z'%3E%3C/path%3E%3C/svg%3E");
}
</style>