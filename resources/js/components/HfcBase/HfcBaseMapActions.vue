<template>
<div class="d-flex flex-wrap flex-lg-nowrap align-self-start align-items-center" style="width: 100%;">
    <div v-if="showselect" class="d-flex flex-column align-items-baseline mx-3 mb-4"  style="flex:1 1;">
            <div class="mr-2 mb-2" style="min-width: 150px">
                {{ viewFilter }}:
            </div>
            <select2 v-cloak v-model.lazy="devices" @input="updateMap" :initial-value="i18nAll" style="min-width: 200px;max-width:400px;" :multiple="true" :i18n="{ all: i18nAll}">
                <option :value="i18nAll" v-text="i18nAll"></option>
                <template v-for="(name, id) in models" :key="id">
                    <option :value="name" v-text="name"></option>
                </template>
            </select2>
    </div>
    <div class="mx-3" v-if="showhfparameters">
        <ul class="nav nav-pills align-self-end m-auto">
            <li 
               v-for="(val, key) in hfParameters"
               :key="key"
               role="presentation" 
               :class="{active: selectedValue == key }" 
               @click="changeParameter(key)"
            >
                <a href="#">{{ val }}</a>
            </li>
        </ul>
    </div>
</div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import $ from 'jquery'
import select2 from '../Select2.vue'

// prepare default vaules
let queryString = window.location.search;
let urlParams = new URLSearchParams(queryString);
let displayValue = urlParams.get('row') ? urlParams.get('row') : 'us_pwr'
let propData = document.querySelector('#map-actions').dataset
let points = propData.points ? JSON.parse(propData.points) : []
let propData_models = propData.models ? JSON.parse(propData.models) : []
let propData_showselect = parseInt(propData.showselect) ? true : false
let propData_showhfparameters = parseInt(propData.showhfparameters) ? true : false

// refs
const viewFilter = ref(propData.viewfilter)
const i18nAll = ref(propData.i18nall)
const selectedValue = ref('')
const models = ref(propData_models)
const devices = ref([])
const hfParameters = ref([])
const showselect = ref(propData_showselect)
const showhfparameters = ref(propData_showhfparameters)

// methods
function changeParameter (selected) {
    selectedValue.value = selected
    displayValue = selected

    updateMap()
}
function updateMap() {
    if (devices.value.includes(i18nAll.value)) {
        return webGLpointsLayer.redraw({action: 'update', payload: points})
    }

    webGLpointsLayer.redraw({
        action: 'update',
        payload: points
            .map((location) => (location.filter((modem) => devices.value.includes(modem.model))))
            .filter((position) => position.length)
    })
}
async function fetchHfParameters(){
    const res = await axios.get("/admin/api/HfcBase/map-actions")
    if(res.data.success){
        hfParameters.value = res.data.result
    }
}       

// mounted
onMounted(async () => {
    fetchHfParameters()
    selectedValue.value = displayValue
    devices.value.push(i18nAll.value)
})
</script>
