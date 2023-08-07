<script setup>
import {ref, onMounted} from 'vue';
import { useFloating, flip, arrow, offset } from '@floating-ui/vue';

onMounted(() => console.log('mounted popover'))
const props = defineProps({
    title: String,
    content: String,
    placement: {
      type: String,
      default: 'right'
    }
})

const show = ref(false)

const floatingArrow = ref(null)
const reference = ref(null)
const floating = ref(null)
const arrowStyles = ref({})
const arrowClasses = ref([])

const {floatingStyles, middlewareData, placement} = useFloating(reference, floating, {
  placement: props.placement,
  middleware: [
    offset(5),
    flip(),
    arrow({element: floatingArrow})
  ],
})

function showPopover() {
  show.value = true

  setTimeout(() =>{
    if (middlewareData.value.arrow) {
      let {x, y} = middlewareData.value.arrow

      if (placement.value == 'right') {
        arrowStyles.value = {
          left: `-6px`,
          top: `${y}px`
        }
        arrowClasses.value = ['border-b', 'border-l']
      }

      if (placement.value == 'left') {
        arrowStyles.value = {
          right: `-6px`,
          top: `${y}px`
        }
        arrowClasses.value = ['border-t', 'border-r']
      }

      if (placement.value == 'top') {
        arrowStyles.value = {
          top: `-6px`,
          left: `${y}px`
        }
        arrowClasses.value = ['border-t', 'border-l']
      }

      if (placement.value == 'bottom') {
        arrowStyles.value = {
          bottom: `-6px`,
          left: `${y}px`
        }
        arrowClasses.value = ['border-b', 'border-r']
      }
    }
  }, 25)
}
</script>

<template>
  <div ref="reference" class="cursor-pointer" @mouseenter="showPopover" @mouseleave="show = false">
    <slot></slot>
  </div>

  <Transition enter-from-class="opacity-0" leave-to-class="opacity-0">
    <div v-if="show" ref="floating" :style="floatingStyles" role="tooltip" class="absolute z-20 inline-block w-64 text-sm text-gray-500 transition-opacity duration-500 bg-white border border-gray-200 rounded-lg shadow-sm dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800">
      <div v-if="props.title" class="px-3 py-2 bg-gray-100 border-b border-gray-200 rounded-t-lg dark:border-gray-600 dark:bg-gray-700">
          <h3 class="font-semibold text-gray-900 dark:text-white">{{ props.title }}</h3>
      </div>
      <div class="px-3 py-2">
          <p>{{ props.content }}</p>
      </div>
      <div ref="floatingArrow" v-show="middlewareData.arrow"
        class="absolute h-3 w-3 rotate-45 z-10 bg-white border-gray-200"
        :class="arrowClasses"
        :style="arrowStyles"
      >
      </div>
    </div>
  </Transition>
</template>

30 steaks 2 Marinaden
