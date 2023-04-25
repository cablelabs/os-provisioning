<template>
<div class="nms-modal fixed z-10 inset-0 overflow-y-auto">
  <div class="flex items-center justify-center min-h-screen">
    <Spinner/>
  </div>
</div>
</template>

<script setup>
import Spinner from '@/components/Spinner.vue'
</script>

<style scoped>
.nms-modal {
  background-color: #333333aa;
}
</style>
