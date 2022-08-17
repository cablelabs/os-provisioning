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
    class="nms-select2"
  >
    <slot></slot>
  </select>
</template>

<script setup>
import { ref, computed, onMounted, onDeactivated } from 'vue'
import $ from 'jquery'
import 'select2'

const emit = defineEmits(['update:modelValue', 'input', 'change'])

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
    return select.value.on('select2:select select2:unselect', (e) => {
      setValue($(select.value).val())
      emit('input', $(select.value).val())
      emit('change', $(select.value).val())
    })
  }

  i18nAll.value = ''
  if (props.i18n) {
    i18nAll.value = props.i18n.all
  }

  select.value.on('select2:select', (e) => onSelect(e.params.data.id))
  select.value.on('select2:unselect', (e) => onUnselect(e.params.data.id))
})

function onSelect(value) {
  if (value == i18nAll.value) {
    selected.value = []
  }

  if (value != i18nAll.value && selected.value.includes(i18nAll.value)) {
    selected.value.splice(selected.value.indexOf(i18nAll.value), 1)
  }

  selected.value.push(value)

  publishChanges()

  // reset values since you don't want to push multiple tasks when executing another task
  if (props.asArray) {
    selected.value = []
  }
}

function onUnselect(value) {
  if (value == i18nAll.value) {
    selected.value = []
  } else {
    selected.value.splice(selected.value.indexOf(value), 1)
  }

  publishChanges()
}

function publishChanges() {
  $(select.value).val(selected.value)
  emit('update:modelValue', selected.value)
  emit('input', selected.value)
  emit('change', selected.value)
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
