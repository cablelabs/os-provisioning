<template>
    <div v-if="store.overlay" class="fixed flex items-center justify-center w-full h-full bg-gray-500/50" >
      <i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
    </div>
</template>

<script setup>
    import { store } from './../store/store'
</script>
