<template>
  <div class="transition hover:bg-gray-100" :class="{ 'active bg-gray-100': visible }">
    <!-- header -->
    <div @click="open" class="accordion-header cursor-pointer transition flex p-3 items-center text-lg">
      <IconMinus v-if="visible" />
      <IconPlus v-else />
      <h3 class="ml-1">
        <slot name="header"></slot>
      </h3>
    </div>
    <!-- Content -->
    <transition 
      name="accordion"
      @enter="start"
      @after-enter="end"
      @before-leave="start"
      @after-leave="end"
    >
      <div class="accordion-content p-3" v-show="visible">
        <slot name="content"></slot>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, inject, computed, onBeforeMount, onMounted } from 'vue'

import IconPlus from '@/components/icons/IconPlus'
import IconMinus from '@/components/icons/IconMinus'

// inject
const Accordion = inject('Accordion')

// refs
const index = ref(null)

// computed
const visible = computed(() => {
  return Accordion.multiple ? Accordion.active.includes(index.value) : index.value == Accordion.active
})

//onBeforeMount
onBeforeMount(() => {
  index.value = Accordion.count++;
})

// mounted
onMounted(() => {
  if(
    (Accordion.multiple && Accordion.active.includes(index.value)) ||
    (!Accordion.multiple && index.value === Accordion.active)
  ) {
    emit('change', true)
  }
})

// emits
const emit = defineEmits(['change'])

// methods
function open() {
  if (visible.value) {
    Accordion.active = Accordion.multiple ? Accordion.active.filter(el => el !== index.value) : null
    emit('change', false)
  } else {
    Accordion.multiple ? Accordion.active.push(index.value) : Accordion.active = index.value;
    emit('change', true)
  }
}

function start(el) {
  el.style.height = el.scrollHeight + "px";
}

function end(el) {
  el.style.height = "";
}
</script>

<style scope>
.accordion-enter-active,
.accordion-leave-active {
  will-change: height, opacity;
  transition: height 0.3s ease, opacity 0.3s ease;
  overflow: hidden;
}
.accordion-enter,
.accordion-leave-to {
  height: 0 !important;
  opacity: 0;
}
</style>
