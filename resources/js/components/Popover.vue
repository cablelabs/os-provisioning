<script setup>
import { ref } from 'vue';
import { useFloating, flip, arrow, offset, autoUpdate } from '@floating-ui/vue';

const props = defineProps({
    title: String,
    content: String,
    placement: {
      type: String,
      default: 'right'
    },
    offset: {
      type: Number,
      default: 14
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
  whileElementsMounted: autoUpdate,
  middleware: [
    offset(props.offset),
    flip(),
    arrow({element: floatingArrow})
  ],
})

function showPopover() {
  show.value = true

  setTimeout(positionArrow, 25)
}

function positionArrow() {
  if (! middlewareData.value.arrow) {
    return
  }

  let {x, y} = middlewareData.value.arrow
  let side = placement.value.split('-')[0];

  const lookup = {
    right: {
      styles: {top: `${y}px`, left: '-6px'},
      classes: ['border-b', 'border-l']
    },
    left: {
      styles: {top: `${y}px`, right: '-6px'},
      classes: ['border-t', 'border-r']
    },
    top: {
      styles: {left: `${x}px`, top: '-6px'},
      classes: ['border-t', 'border-l']
    },
    bottom: {
      styles: {left: `${x}px`, bottom: '-6px'},
      classes: ['border-b', 'border-r']
    },
  }

  arrowStyles.value = lookup[side].styles
  arrowClasses.value = lookup[side].classes
}

</script>

<template>
  <div>
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
  </div>
</template>

30 steaks 2 Marinaden
