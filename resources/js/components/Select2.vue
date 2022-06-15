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

<template>
  <select
    :id="id"
    :name="name"
    :disabled="disabled"
    :required="required"
    ref="select"
  >
    <slot></slot>
  </select>
</template>

<script setup>
import { ref, computed, onMounted, onDeactivated, nextTick } from 'vue'
import $ from 'jquery'
import 'select2'
import { store } from './../store/store'

const emit = defineEmits(['update:modelValue', 'updateref'])

const props = defineProps({
  modelValue: {
    type: [String, Array, Number]
  },
  id: {
    type: String,
    default: ''
  },
  name: {
    type: String,
    default: ''
  },
  options: {
    type: Array,
    default: () => []
  },
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  initial: {
    type: Number,
    default: 0
  },
  i18n: {
    type: Object,
    default: {}
  },
  multiple: {
    type: Boolean,
    default: false
  },
  asArray: {
    type: Boolean,
    default: false
  }
})

const select = ref(null)
const selected = ref(null)
const i18nAll = ref('all')

onMounted(() => {
  select.value = $(select.value)
  selected.value = props.modelValue

  if (props.initial) {
    setValue(props.multiple || props.asArray ? [props.initial] : props.initial)
  }

  select.value
    .select2({
      data: props.options,
      multiple: props.multiple
    })
    .val(selected.value)
    .trigger('change')

  if (!props.multiple && !props.asArray) {
    select.value.on('select2:select select2:unselect', (e) => {
      setValue($(select.value).val())
    })
  }

  i18nAll.value = ''
  if (props.i18n) {
    i18nAll.value = props.i18n.all
  }

  select.value.on('select2:select', (e) => onSelect(e.params.data.id))
  select.value.on('select2:unselect', (e) => onUnselect(e.params.data.id))
})

async function onSelect(value) {
  store.overlay = true;

  const res = await axios.get(`/admin/CoreMon/api/v0/Market/${value}`);

  res.data.result.forEach(async (el) => {
    if(!el.active){
      el.type = el.type === 'Net' ? 'Network' : el.type

      const prevSelect = $(`[id='${el.type}']`)

      retrySelect2(prevSelect, ["RemoveOptions", "AddOption"], {name: el.name, value: el.id})
    }
  })

  refreshNextSelects()

  publishChanges()

  store.overlay = false;
}

async function onUnselect(value) {
  refreshNextSelects()

  // refreshPrevSelects()
  
  await nextTick()

  retrySelect2($(select.value), ['RemoveOptions'])  

  publishChanges()
}

function refreshNextSelects() {
  const _colItem = $(select.value).closest('.col-item')

  let _next = _colItem.next()
  while(_next.length) {
    retrySelect2(_next.find('select'), ['RemoveOptions'])
    _next = _next.next();
  }
}

function refreshPrevSelects() {
  const _colItem = $(select.value).closest('.col-item')

  let _prev = _colItem.prev()
  while(_prev.length) {
    retrySelect2(_prev.find('select'))
    _prev = _prev.prev();
  }
}

async function retrySelect2(target, options=[], payload = {}) {
  target.select2("destroy")

  if(options.includes("RemoveOptions")) {
    target.find("option").remove()
  }

  if(options.includes("AddOption")) {
    const newOption = new Option(payload.name, payload.value, true, true)
    target.append(newOption).trigger('change')
    emit('updateref', {ref: target.data('model'), value: payload.value ? payload.value : 0})
  } else {
    target.val(0)
    emit('updateref', {ref: target.data('model'), value: 0})
    target.parent().find("a").attr("href", "#")
  }

  await nextTick()
  window.initAjaxSelect2(target)
}

function publishChanges() {
  $(select.value).val(selected.value)
  emit('update:modelValue', selected.value)
}

function setValue(val) {
  select.value = $(select.value)

  if (val instanceof Array) {
    select.value.val([...val])
  } else {
    select.value.val([val])
  }

  select.value.trigger('change')
  selected.value = val
  emit('update:modelValue', val)
}

onDeactivated(() => {
  select.value.off().select2('destroy')
})
</script>
