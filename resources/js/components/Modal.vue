<template>
<div class="nms-modal fixed z-10 inset-0 overflow-y-auto" :class="{ 'hidden': !modelValue }">
  <div class="flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" :aria-hidden="modelValue ? 'false' : 'true'" :class="{ 'translate-y-0': modelValue, '-translate-y-full': !modelValue }">
      <div class="bg-gray-50 px-4 py-3 border-b">
        <slot name="header"></slot>
      </div>
      <div class="bg-white px-4 py-3">
        <slot name="content"></slot>
      </div>
      <div class="bg-gray-50 px-4 py-3 flex justify-end">
        <slot name="footer"></slot>
      </div>
    </div>
  </div>
</div>
</template>

<script setup>
// props
const props = defineProps({
  modelValue: { type: Boolean, default: false },
})
</script>

<style scoped>
.nms-modal {
  background-color: #333333aa;
}
</style>
