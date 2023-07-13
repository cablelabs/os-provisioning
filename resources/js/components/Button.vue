<template>
<button @click="$emit('click')" :disabled="isLoading" 
  class="text-white font-bold py-2 px-4 rounded flex items-center"
  :class="{ 'bg-green-500 hover:bg-green-700': type === 'success', 'bg-red-500 hover:bg-red-700': type === 'danger', 'bg-blue-500 hover:bg-blue-700': type === 'primary' }"
>
  <Spinner v-if="isLoading" size="4" border="2" class="mr-1" />
  {{ label }}
</button>
</template>

<script setup>
import Spinner from '@/components/Spinner.vue'

// props
const props = defineProps({
  label: { type: String, default: 'Button' },
  type: { type: String, default: 'primary' },
  isLoading: { type: Boolean, default: false },
})

const emit = defineEmits(['click'])
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}
</style>
