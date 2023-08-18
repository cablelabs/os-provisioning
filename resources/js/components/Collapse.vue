<template>
    <slot :show="show" :toggleShow="toggleShow" />
</template>

<script setup>
import { ref } from 'vue'

const show = ref(false)

function toggleShow() {
 show.value = !show.value
}
</script>
