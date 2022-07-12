<template>
  <svg :width="size" :height="size" :color="color" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="transform transition-transform duration-500 ease-in-out"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
</template>

<script setup>
import { store } from '@/store/store'

const props = defineProps({
  color: { type: String, default: store.icons.defaultColor },
  size: { type: [String, Number], default: store.icons.defaultSize },
})
</script>
