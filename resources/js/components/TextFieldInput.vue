<template>
  <div class="relative">
    <input
      :value="modelValue"
      @focus="isFocused = true"
      @blur="isFocused = false"
      @input="$emit('update:modelValue', $event.target.value)"
      class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg outline-none transition-all duration-300"
    >
    <label
      :class="{ 'text-gray-500 top-2 left-3': !modelValue && !isFocused, 'text-blue-500 -top-2 left-1': isFocused || modelValue }"
      class="absolute px-1 text-sm transition-all duration-200 bg-white"
    >
      {{ label }}
    </label>
    <div
      :class="{ 'bg-blue-500': isFocused || modelValue, 'bg-gray-300': !isFocused && !modelValue }"
      class="absolute bottom-0 left-0 w-0 h-0 rounded-full transition-all duration-300"
      style="
        transform: translateX(-50%) translateY(-50%);
        top: 50%;
        left: 50%;
      "
    ></div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

// props
const props = defineProps({
  modelValue: { type: String, default: '' },
  label: { type: String, default: 'label' },
})

// refs
const isFocused = ref(false)

// emits
const emit = defineEmits(['update:modelValue'])
</script>
