<template>
  <div class="relative min-w-[130px]" ref="componentRef">
    <input
      ref="inputRef"
      v-model="search"
      class="w-100 appearance-none border-2 rounded p-2 text-gray-700 leading-tight hover:cursor-pointer outline-0" 
      :class="{'outline-none bg-white border-green-500' : open, 'bg-gray-200 border-gray-200': !open}"
      type="text"
      :placeholder="`${selected.length} ${placeholder}`"
      :readonly="disableSearch || !open"
      @click="clickSearchInput"
    />
    <div @click="focusInput" :class="{'rotate-180': !open}" class="absolute top-1.5 right-1.5 hover:cursor-pointer">
      <IconChevron />
    </div>
    <div class="w-100 mt-1 rounded-sm bg-white p-2 shadow-md absolute" v-show="open" v-if="filteredOptions.length">
      <div v-if="!hideSelectAll" class="flex items-center mb-1 checkboxes">
        <input v-model="selectAll" @click="handleSelectAll" :id="`checkbox-selectAll-${randomValue}`" type="checkbox" class="mt-0 w-4 h-4 text-green-600 rounded accent-green-500 hover:cursor-pointer">
        <label :for="`checkbox-selectAll-${randomValue}`" class="mb-0 pl-2 text-gray-900 min-w-fit hover:text-green-500 hover:cursor-pointer">Alle auswählen</label>
      </div>
      <div class="flex items-center mb-1 checkboxes" v-for="(option, i) in filteredOptions" :key="i">
        <input v-model="selected" :id="`checkbox-${randomValue}-${i}`" type="checkbox" :value="option" class="mt-0 w-4 h-4 text-green-600 rounded accent-green-500 hover:cursor-pointer">
        <label :for="`checkbox-${randomValue}-${i}`" class="mb-0 pl-2 text-gray-900 min-w-fit hover:text-green-500 hover:cursor-pointer">{{ typeof option === 'object' ? option.name : option }}</label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import IconChevron from '@/components/icons/IconChevron.vue'

// emits
const emit = defineEmits(['update:modelValue'])

// props
const props = defineProps({
  modelValue: { type: Array, default: [] },
  options: { type: Array, default: [] },
  disableSearch: { type: Boolean, default: false },
  enableClickOutside: { type: Boolean, default: false },
  hideSelectAll: { type: Boolean, default: false },
  placeholder: { type: String, default: 'ausgewählt' },
})

// refs
const selected = ref([])
const inputRef = ref(null)
const search = ref('')
const open = ref(false)
const randomValue = ref(Math.floor(Math.random() * 1000000))
const componentRef = ref(null);
const selectAll = ref(false)

// computed
const filteredOptions = computed(() => {
  return props.disableSearch ? props.options : (search.value ? props.options.filter(function (el) {
    const target = typeof el === 'object' ? el.name : el

    return target.toLowerCase().includes(search.value.toLowerCase())
  }) : props.options)
})

// mounted
onMounted(() => {
  if (props.enableClickOutside) {
    document.addEventListener('click', handleClickOutside);
  }
});

onBeforeUnmount(() => {
  if (props.enableClickOutside) {
    document.removeEventListener('click', handleClickOutside);
  }
});

// watch
watch(open, (newValueOpen) => {
  if (! newValueOpen) {
    search.value = ''
  }
});

watch(selected, (newValueSelected, oldValueSelected) => {
  emit('update:modelValue', newValueSelected)

  if (oldValueSelected.length > newValueSelected.length) {
    selectAll.value = false
  }

  if (newValueSelected.length === props.options.length) {
    selectAll.value = true
  }
})

// methods
function focusInput() {
  inputRef.value.focus()
  openDropDown()
}

function clickSearchInput() {
  if (open.value && ! props.disableSearch) {
    return
  }

  openDropDown()
}

function openDropDown() {
  open.value = !open.value
}

function handleClickOutside(event) {
  if (componentRef.value && !componentRef.value.contains(event.target)) {
    open.value = false
  }
}

function handleSelectAll() {
  selected.value = []
  if (! selectAll.value) {
    search.value = ''
    props.options.forEach(el => selected.value.push(el))
  }
}
</script>
