<template>
  <div class="accordion">
    <slot></slot>
  </div>
</template>

<script setup>
import { reactive, provide } from 'vue'

// props
const props = defineProps({
  active: { type: [Number, String, Array], default: null },
  multiple: { type: Boolean, default: false },
})

// reactive
let Accordion = reactive({
  count: 0,
  active: props.multiple ? props.active !== null ? props.active.map(el => parseInt(el)) : [] : props.active !== null ? parseInt(props.active) : null,
  multiple: props.multiple
})

// provide
provide('Accordion', Accordion)
</script>
