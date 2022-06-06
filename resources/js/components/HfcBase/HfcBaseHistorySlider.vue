<template>
<div>
    <h4 style="width:5.5rem">Outages:</h4>
    <h4 style="width:5.5rem;">Proactive:</h4>
</div>
<div class="d-flex align-items-center justify-content-center" @click="decrease">
    <i class="fa fa-caret-left fa-3x" :class="{ 'text-primary': isDecreasePossible }" :style="isDecreasePossible ? 'cursor:pointer': ''"></i>
</div>
<div style="flex:1 auto" class="d-flex flex-column">
    <div id="slide1" style="border: 1px solid white;height:30px;flex:1 auto" class="d-flex align-items-center m-l-10 m-r-10">
        <div v-for="(state, index) in sliderOutage" :key="index"
            :class="state[1] >= 2 ? 'bg-danger' : (state[1] == 1 ? 'bg-warning' : 'bg-success')"
            :style="`height:15px;width:${ width }%;`"
            @mouseenter="enter(state, index)"
            :id="'online-' + index"
            @mouseleave="leave(index, width)"
        >
        </div>
    </div>
    <div id="slide2" style="border: 1px solid white;height:30px;flex:1 auto" class="d-flex align-items-center m-l-10 m-r-10">
        <div v-for="(state, index) in sliderProactive" :key="index"
            :class="state[1] >= 2 ? 'bg-danger' : (state[1] == 1 ? 'bg-warning' : 'bg-success')"
            :style="`height:15px;width:${width}%;`"
            @mouseenter="enter(state, index)"
            :id="`power- + ${index}`"
            @mouseleave="leave(index, width)"
        >
        </div>
    </div>
</div>
<div class="d-flex align-items-center justify-content-center" @click="increase">
    <i class="fa fa-caret-right fa-3x" :class="{ 'text-primary': isIncreasePossible }" :style="isIncreasePossible ? 'cursor:pointer': ''"></i>
</div>
<div class="d-flex flex-column align-items-center justify-content-center" style="width:10rem;">
    <h4 v-text="selected"></h4>
    <template v-if="sliderOutage">
        <div style="font-size: 12px;" v-if="sliderOutage.length">
            <span v-text="from"></span> - <span v-text="to"></span>
        </div>
    </template>
</div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { store } from './../../store/store'

const default_width = 100 / (7 * 24);

// refs
const selected = ref('Select a date')
const start = ref(-169)
const interval = ref(168)
const width = ref(default_width)


// computed
const sliderProactive = computed(() => {
    return !store.hfcStorageRequest.proactive ? [] : Object.entries(store.hfcStorageRequest.proactive).slice(start.value, start.value + interval.value)
})
const sliderOutage = computed(() => {
    return !store.hfcStorageRequest.outage ? [] : Object.entries(store.hfcStorageRequest.outage).slice(start.value, start.value + interval.value)
})
const isDecreasePossible = computed(() => {
    return !store.hfcStorageRequest.proactive ? false : (start.value - interval.value) > (Object.keys(store.hfcStorageRequest.outage).length * - 1)
})
const isIncreasePossible = computed(() => {
    return (start.value + interval.value) < -1
})
const from = computed(() => {
    return sliderOutage.value[0][0]
})
const to = computed(() => {
    return sliderOutage.value[sliderOutage.value.length - 1][0]
})

// mounted
onMounted(()=>{
})

// methods
function enter(state, index) {
    document.getElementById('power-' + index).style.cssText += 'height:45px;border: 1px solid white;'
    document.getElementById('online-' + index).style.cssText  += 'height:45px;border: 1px solid white;'
    selected.value = state[0]
}
function leave(index, width) {
    document.getElementById('power-' + index).style.cssText = 'height:15px;width:' + width + '%;'
    document.getElementById('online-' + index).style.cssText = 'height:15px;width:' + width + '%;'
}
function increase() {
    if (isIncreasePossible.value) {
        start.value = start.value + interval.value
    }
}
function decrease() {
    if (isDecreasePossible.value) {
        start.value = start.value + interval.value
    }
}

</script>
