<template>
<div 
  class="border-gray-300 border-t-blue-500 rounded-full animate-spin"
  :class="`w-${size} h-${size} border-${border} border-t-${border}`"
></div>
</template>

<script setup>
// props
const props = defineProps({
  size: { type: [String, Number], default: 12 },
  border: { type: [String, Number], default: 4 },
})
</script>
