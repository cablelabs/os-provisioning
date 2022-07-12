<template>
  <div class="accordion">
    <slot></slot>
  </div>
</template>

<script setup>
import { reactive, provide } from 'vue'

// props
const props = defineProps({
  active: { type: [Number, String], default: null },
})

// reactive
let Accordion = reactive({
  count: 0,
  active: props.active ? parseInt(props.active) : null,
})

// provide
provide('Accordion', Accordion)
</script>
