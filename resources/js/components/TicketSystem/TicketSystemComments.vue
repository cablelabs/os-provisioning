<template>
<div class="panel-body fader" style="overflow-y:auto; height:100%;">
  <ul v-if="comments.length" id="comments" class="media-list" v-cloak style="max-height: 400px;overflow-y: scroll;">
    <li v-for="comment in comments" class="media">
      <img :src="propData.supportAvatar" width="30" alt="" class="mr-3 rounded-circle">
      <div class="media-body">
        <span class="text-muted pull-right">
          <small class="text-muted" v-text="comment.created_at"></small>
        </span>
        <strong class="text-success" v-text="comment.user.first_name + ' ' + comment.user.last_name"></strong>
        <p class="mb-0" style="white-space: pre-wrap;" v-html="comment.comment">
        </p>
      </div>
    </li>
  </ul>
  <hr v-if=comments.length>
  <textarea v-model="new_comment" class="form-control d-print-none" :placeholder="propData.ticketWriteComment" rows="4"
    style="font-size: 16px"></textarea>
  <br>
  <button v-on:click="save(ticket.id)" type="button" class="btn btn-primary m-r-5 m-t-15 pull-right"
    name="_save" value="1" title="">
    <i class="fa fa-lg m-r-10" :class="loading ? 'fa-circle-o-notch fa-spin' : 'fa-send'" aria-hidden="true"></i>
    {{ propData.ticketCommentSend }}
  </button>
  <div class="clearfix"></div>
</div>
</template>

<script setup>
import { ref, onMounted, computed, onBeforeMount } from 'vue'

// prepare default vaules
let propData = document.querySelector('#ticketsystem-comments').dataset
const propDataViewVar = propData.viewVar ? JSON.parse(propData.viewVar) : {}

// refs
const loading = ref(true)
const ticket = ref(propDataViewVar)
const comments = ref([])
const user_id = ref(propData.userId)
const new_comment = ref('')

// mounted
onMounted(() => {
  getComments()
})

// on before mount
onBeforeMount(() => {
  scrollDown()
})

// methods
function save(ticket_id) {
  new_comment.value = new_comment.value.trim()

  if (new_comment.value === '') {
  return;
  }

  loading.value = true

  axios({
  method: 'post',
  url: '/admin/api/v1/Comment',
  contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
  headers: {'Authorization': `Bearer ${propData.apiToken}`},
  data: {ticket_id: ticket_id, comment: new_comment.value, user_id: user_id.value}
  })
  .then((response) => {
    getComments()
    new_comment.value = ''
  })
  .catch((error) => {
    alert(error)
  });
}

function getComments() {
  axios({
  method: 'get',
  url: '/admin/api/v1/Comment',
  contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
  headers: {'Authorization': `Bearer ${propData.apiToken}`},
  params: {
    filter_groups: [{
    filters: [{
      key: 'ticket_id',
      value: ticket.value.id,
      operator: 'eq',
      not: false
    }], or: false
    }], includes: ['user'], sort: [{key: 'created_at', direction: 'asc'}]
  }
  }).then((response) => {
  comments.value = response.data
  loading.value = false
  }).then(() => {
  scrollDown()
  }).catch((error) => {
  alert(error)
  });
}

function scrollDown() {
  if (document.getElementById("comments") && comments.value.length) {
  document.getElementById("comments").scrollTop = document.getElementById("comments").scrollHeight;
  }
}
</script>
