<template>
<div>
  <label class="relative inline-flex items-center cursor-pointer">
  <input :checked="value" type="checkbox" class="sr-only peer">
  <div :class="[widthClass, heightClass, afterTopClass, afterLeftClass, afterHClass, afterWClass]" class="bg-gray-200 rounded-full peer peer-focus:ring-4 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:bg-white after:border-gray-300 after:border after:rounded-full after:transition-all peer-checked:bg-green-600"></div>
  <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300" v-if="text">{{ text }}</span>
</label>
</div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'

// props
const props = defineProps({
  value: { type: Boolean, default: false },
  size: { type: String, default: 'medium' },
  text: { type: String, default: '' },
})

// refs
const widthClass = ref('w-11')
const heightClass = ref('h-6')
const afterTopClass = ref('after:top-[2px]')
const afterLeftClass = ref('after:left-[2px]')
const afterHClass = ref('after:h-5')
const afterWClass = ref('after:w-5')

switch(props.size) {
  case 'small':
    widthClass.value = 'w-9'
    heightClass.value = 'h-5'
    afterTopClass.value = 'after:top-[2px]'
    afterLeftClass.value = 'after:left-[2px]'
    afterHClass.value = 'after:h-4'
    afterWClass.value = 'after:w-4'
    break
  case 'medium':
    widthClass.value = 'w-11'
    heightClass.value = 'h-6'
    afterTopClass.value = 'after:top-[2px]'
    afterLeftClass.value = 'after:left-[2px]'
    afterHClass.value = 'after:h-5'
    afterWClass.value = 'after:w-5'
    break
  case 'large':
    widthClass.value = 'w-14'
    heightClass.value = 'h-7'
    afterTopClass.value = 'after:top-0.5'
    afterLeftClass.value = 'after:left-[4px]'
    afterHClass.value = 'after:h-6'
    afterWClass.value = 'after:w-6'
    break
}
</script>

<style scopped>
input:focus {
  outline: none;
}
</style>
