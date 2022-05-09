<template>
  <select ref="select">
    <slot></slot>
  </select>
</template>

<script setup>
import { ref, onMounted, watch, onDeactivated } from 'vue'
import $ from 'jquery'
import 'select2'

const props = defineProps({
  options: [Object, Array],
  initialValue: {
    type: [String, Number, Array],
    default: ''
  },
  i18n: {
    type: Object,
    default: false
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
const value = ref([])
const i18nAll = ref('all')

onMounted(() => {
  select.value = $(select.value)

  if (props.initialValue) {
    value.value =
      props.multiple || props.asArray
        ? [props.initialValue]
        : props.initialValue
  }

  i18nAll.value = ''
  if (props.i18n) {
    i18nAll.value = props.i18n.all
  }

  select.value
    .select2({
      data: props.options,
      multiple: props.multiple
    })
    .val(value.value)
    .trigger('change')

  if (!props.multiple) {
    return select.value.on('change', (e) => emit('input', e.target.value))
  }

  select.value.on('select2:select', (e) => selected(e.params.data.id))
  select.value.on('select2:unselect', (e) => unselected(e.params.data.id))
})

const emit = defineEmits(['input'])

function selected(value) {
  if (value == i18nAll.value) {
    value.value = []
  }

  if (value != i18nAll.value && value.value.includes(i18nAll.value)) {
    value.value.splice(value.value.indexOf(i18nAll.value), 1)
  }

  if (props.multiple || props.asArray) {
    value.value.push(value)
  }

  publishChanges()
}

function unselected(value) {
  if (value == i18nAll.value) {
    return emit('input', [])
  }

  value.value.splice(value.value.indexOf(value), 1)
  publishChanges()
}

function publishChanges() {
  emit('input', value.value)
  select.value.val(value.value).trigger('change')
}

watch(props.options, (options) => {
  select.value.empty().select2({ data: options })
})

onDeactivated(() => {
  select.value.off().select2('destroy')
})
</script>
