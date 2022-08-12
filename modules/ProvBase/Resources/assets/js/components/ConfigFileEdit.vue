<template>
  <h2 class="mb-3 text-3xl font-medium leading-tight">{{ translations.dragdrop }}
    <a data-toggle="popover" data-html="true" data-container="body" data-trigger="hover" title="" data-placement="right"
      :data-content="translations.infotext"
      :data-original-title="translations.infoheader">
      <i class="fa p-t-5 fa-question-circle text-info"></i>
    </a>
  </h2>

  <div class="flex flex-col gap-6 xl:flex-row">
    <div class="flex flex-col w-full xl:w-1/2" id="left">
      <draggable v-model="lists" item-key="name" group="g1" handle=".fa-bars" filter="input" :prevent-on-filter="false" @change="refreshJson();">
        <template #item="{ element: list, index: key}">
          <div v-if="key != '0'" class="pb-6">
            <div class="flex items-baseline text-xl font-normal">
              <i class="fa fa-bars cursor-grabbing" aria-hidden="true"></i>
              <div class="flex flex-col self-center px-1 text-xs divide-y-1">
                <i v-show="key != 1" class="cursor-pointer fa fa-arrow-up" aria-hidden="true" @click="moveUp(key)"></i>
                <i v-show="key != lists.length - 1" class="cursor-pointer fa fa-arrow-down" aria-hidden="true" @click="moveDown(key)"></i>
              </div>
              <input type="text" class="flex-1 p-2 bg-transparent" v-model.lazy="list.name" @keypress.enter.prevent.stop="blurInput" @blur="setType(list);refreshJson();">
              <div class="p-2 bg-transparent">
                <select2 name="listtype" v-model="list.type" @change="renameTitle(list);refreshJson();">
                  <option value="list">{{ translations.listtype.list }}</option>
                  <option value="table">{{ translations.listtype.table }}</option>
                  <option value="paginated">{{ translations.listtype.paginated }}</option>
                </select2>
              </div>
              <button class="btn btn-primary" @click.prevent="delList(key);refreshJson();">
                {{ translations.deleteList }}
              </button>
            </div>
            <draggable class="border-2 border-dashed border-slate-600 min-h-[5rem] bg-gray-200" v-model="list.content" item-key="id" group="g2" draggable=".cursor-grabbing" filter="input" :prevent-on-filter="false" @change="refreshJson();">
              <template #item="{ element: element, index: id }">
                <div class="relative p-2 mb-1 bg-gray-200 cursor-grabbing">
                  <div class="flex flex-col p-2 bg-gray-100">
                    <div class="relative flex pb-2 justify-content-between" :class="element.id">
                      <div class="font-bold break-all" v-text="element.id"></div>
                      <i class="pl-4 cursor-pointer fa fa-cog" aria-hidden="true" @click="itemmenu(element)"></i>
                      <div class="absolute z-10 p-0 bg-gray-100 border-2 border-gray-500 top-6 right-2" :class="element.menu ? 'flex flex-col' : 'hidden'">
                        <template v-for="(listname, listkey) in lists" :key="listkey">
                          <span class="p-1 border border-gray-400 cursor-pointer hover:bg-gray-300" v-if="listkey != '0' && listkey != key" @click="moveItem(key,listkey, id)">
                            {{ translations.moveTo }} {{ listname.name }}
                          </span>
                        </template>
                        <span class="p-1 border border-gray-400 cursor-pointer hover:bg-gray-300" @click="moveItem(key, -1, id)">
                          {{ translations.moveToNewList }}
                        </span>
                        <span class="p-1 border border-gray-400 cursor-pointer hover:bg-gray-300" @click="moveItem(key, 0, id)">
                          {{ translations.deleteElement }}
                        </span>
                      </div>
                    </div>
                    <div class="flex items-center mb-2">
                      <div class="w-40">{{ translations.displayName }}</div>
                      <input :placeholder="translations.displayNamePlaceholder" class="flex-1 p-1" type="text" name="oname" v-model="element.name" @keypress.enter.prevent.stop="blurInput" @blur="refreshJson"/>
                    </div>
                    <div class="flex items-center mb-2">
                      <div class="w-40">{{ translations.analysisOperator }}</div>
                      <div class="flex-1">
                        <select2 data-allow-clear="true" :data-placeholder="translations.operatorPlaceholder" name="calcOp" v-model="element.calcOp" @change="refreshJson">
                          <option value=""></option>
                          <option value="+">{{ translations.add }} (+)</option>
                          <option value="-">{{ translations.sustract }} (-)</option>
                          <option value="*">{{ translations.multiply }} (*)</option>
                          <option value="/">{{ translations.divide }} (/)</option>
                          <option value="%">{{ translations.modulo }} (%)</option>
                        </select2>
                      </div>
                    </div>
                    <div class="flex items-center mb-2">
                      <div class="w-40">{{ translations.analysisOperand }}</div>
                      <input class="flex-1 p-1" :placeholder="translations.analysisOperandPlaceholder" type="number" step="0.0001" name="calcVal" v-model.number="element.calcVal" @keypress.enter.prevent.stop="blurInput" @blur="refreshJson"/>
                    </div>
                    <div>
                      <div class="flex items-center mb-2">
                        <div class="w-40">{{ translations.monitorInDiagram }}</div>
                        <div class="flex justify-center flex-1">
                          <input title="Monitor?" type="checkbox" class="toggleColorizeParams" name="colorize" v-model="element.monitorInDiagram" @keypress.enter.prevent.stop="blurInput">
                        </div>
                      </div>
                      <div v-show="element.monitorInDiagram">
                        <div class="flex items-center mb-2">
                          <div class="w-40">{{ translations.diagramColumn }}</div>
                          <div style="flex:1 1 100px;min-width:0;">
                            <select2 data-allow-clear="true" :data-placeholder="translations.diagramColumnPlaceholder" name="diagramVar" v-model="element.diagramVar" @change="refreshJson">
                              <option value=""></option>
                              <option v-for="(column, columnKey) in propDataColumns" :value="column" :key="columnKey">
                                {{ column }}
                              </option>
                            </select2>
                          </div>
                        </div>
                        <div class="flex items-center mb-2">
                          <div class="w-40">{{ translations.diagramOperator }}</div>
                          <div class="flex-1">
                            <select2 data-allow-clear="true" :data-placeholder="translations.operatorPlaceholder" name="diagramOp" v-model="element.diagramOp" @change="refreshJson">
                              <option value=""></option>
                              <option value="+">{{ translations.add }} (+)</option>
                              <option value="-">{{ translations.sustract }} (-)</option>
                              <option value="*">{{ translations.multiply }} (*)</option>
                              <option value="/">{{ translations.divide }} (/)</option>
                              <option value="%">{{ translations.modulo }} (%)</option>
                            </select2>
                          </div>
                        </div>
                        <div class="flex items-center mb-2">
                          <div class="w-40">{{ translations.diagramOperand }}</div>
                          <input class="flex-1 p-1" :placeholder="translations.diagramOperandPlaceholder" type="number" step="0.0001" name="diagramVal" v-model.number="element.diagramVal" @blur="refreshJson" @keypress.enter.prevent.stop="blurInput" />
                        </div>
                      </div>
                    </div>

                    <div>
                      <div class="flex items-center mb-2">
                        <div class="w-40">{{ translations.colorize }}</div>
                        <div class="flex justify-center flex-1">
                          <input title="Colorize?" type="checkbox" class="toggleColorizeParams" name="colorize" v-model="element.colorize" @keypress.enter.prevent.stop="blurInput" @change="refreshJson">
                        </div>
                      </div>
                      <div v-show="element.colorize" class="flex flex-col">
                        <div class="flex flex-col mb-2">
                          <input type="text" name="colorDanger" class="p-1" style="background-color: #ffddbb;margin-top:.5rem;" :placeholder="propData.configfileDragDropThreshholdsCriticalOrange" :title="propData.configfileDragDropThreshholdsCriticalOrange" v-model="element.colorDanger" @keypress.enter.prevent.stop="blurInput" @blur="refreshJson"/>
                          <input type="text" name="colorWarning" class="p-1" style="background-color: #ffffdd;margin-top:.5rem;" :placeholder="propData.configfileDragDropThreshholdsWarningYellow" :title="propData.configfileDragDropThreshholdsWarningYellow" v-model="element.colorWarning" @keypress.enter.prevent.stop="blurInput" @blur="refreshJson"/>
                          <input type="text" name="colorSuccess" class="p-1" style="background-color: #ddffdd;margin-top:.5rem;" :placeholder="propData.configfileDragDropThreshholdsSuccessGreen" :title="propData.configfileDragDropThreshholdsSuccessGreen" v-model="element.colorSuccess" @keypress.enter.prevent.stop="blurInput" @blur="refreshJson"/>
                        </div>
                        <select2 data-allow-clear="true" :data-placeholder="translations.selectMapParameter" name="valueType" v-model="element.valueType" title="Usage e.g. in topo map" @change="refreshJson">
                          <option value=""></option>
                          <option value="us_pwr">US PWR</option>
                          <option value="us_snr">US SNR</option>
                          <option value="ds_pwr">DS PWR</option>
                          <option value="ds_snr">DS SNR</option>
                        </select2>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
            </draggable>
          </div>
        </template>
      </draggable>

      <div class="flex items-center mt-3 text-xl font-normal">
        <input type="text" class="flex-1 p-2 mr-8 bg-transparent" v-model="listName" :placeholder="translations.listname" @keypress.enter.prevent.stop="addList();refreshJson();" />
        <button class="p-2 btn btn-primary" @click.prevent="addList();refreshJson();">
          {{ translations.addList }}
        </button>
      </div>
    </div>

    <div class="flex flex-col w-full xl:w-1/2" id="right">
      <div class="flex items-center text-xl font-normal">
        <div class="flex-1 p-2 pr-4">{{ translations.deviceParameters }}</div>
        <button @click.prevent="refreshDeviceParams" class="btn btn-primary">
          <i v-if="refresh" class="fa fa-circle-o-notch fa-spin"></i>
          {{ translations.refresh }}
        </button>
      </div>
      <input class="mb-3 text-lg w-100" type="text" @input.prevent="ddFilter" @keypress.enter.prevent.stop='blurInput' v-model="search" :placeholder="propData.buttonSearch"/>
      <draggable class="border-2 border-dashed border-slate-600 min-h-[5rem] bg-gray-200" v-model="lists[0].content" item-key="id" group="g2" filter="input" :prevent-on-filter="false" >
        <template #item="{ element, index }">
          <div class="relative p-2 mb-1 bg-gray-200 cursor-grabbing">
            <div class="flex justify-between pb-2" :class="element.id">
              <div class="font-bold break-all" v-text="element.id"></div>
              <i class="pl-4 cursor-pointer fa fa-cog" aria-hidden="true" @click="itemmenu(element)"></i>
              <div class="absolute z-10 p-0 bg-gray-100 border-2 border-gray-500 top-8 right-2" :class="element.menu ? 'flex flex-col' : 'hidden'">
                <template v-for="(listname, listkey) in lists" :key="listkey">
                  <span class="p-1 border border-gray-400 cursor-pointer hover:bg-gray-300" v-if="listkey != '0'" @click="moveItem(0, listkey, index)">
                    {{ translations.moveTo }} {{ listname.name }}
                  </span>
                </template>
                <span class="p-1 border border-gray-400 cursor-pointer hover:bg-gray-300" @click="moveItem(0, -1, index)">{{ translations.moveToNewList }}</span>
              </div>
            </div>
            <div class="flex items-center">
              <div class="mr-2">{{ translations.displayName }}</div>
              <input class="flex-1 p-1" type="text" name="oname" v-model="element.name" @keypress.enter.prevent.stop="blurInput"/>
            </div>
          </div>
        </template>
      </draggable>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import axios from 'axios'

// prepare default vaules
let propData = document.querySelector('#provbase-config-file-edit').dataset
const propDataLists = propData.lists ? JSON.parse(propData.lists) : {}
const propDataColumns = propData.columns ? JSON.parse(propData.columns) : {}
const translations = JSON.parse(propData.translations)

// refs
const listName = ref('')
const search = ref('')
const prevSearch = ref('')
const searchTimeout = ref(null)
const lists = ref(propDataLists)
const refresh = ref(false)

// mounted
onMounted(() => {
  for (let key = 1; key < lists.value.length; key++) {
    for (let i = 0; i < lists.value[key].content.length; i++) {
      let content = lists.value[key].content[i]
      content.monitorInDiagram = Boolean(
        content.diagramVar || content.diagramOp || content.diagramVal
      )
    }
  }
})

function itemmenu(element) {
  if (!element.hasOwnProperty('menu')) {
    return (element.menu = true)
  }

  element.menu = !element.menu
}

function moveItem(olist, key, id) {
  if (key == -1) {  // for creating a new list
    listName.value = translations.listname
    key = lists.value.length
    addList()
  }

  lists.value[key].content.push({
    id: lists.value[olist].content[id].id,
    name: lists.value[olist].content[id].name,
    calcOp: '',
    calcVal: '',
    diagramVar: '',
    diagramOp: '',
    diagramVal: '',
    colorize: '',
    colorDanger: '',
    colorWarning: '',
    colorSuccess: '',
    valueType: ''
  })

  lists.value[olist].content.splice(id, 1)
  refreshJson()
}

function moveUp(index) {
  if (index > 1) {
    let temp = lists.value[index];
    lists.value[index] = lists.value[index - 1];
    lists.value[index - 1] = temp;
  }
}

function moveDown(index) {
  if (index < lists.value.length - 1) {
    let temp = lists.value[index];
    lists.value[index] = lists.value[index + 1];
    lists.value[index + 1] = temp;
  }
}

function addList() {
  if (!listName.value) {
    return
  }

  lists.value.push({
    name: listName.value,
    content: []
  })

  setType(lists.value[lists.value.length - 1])

  listName.value = ''
}

function delList(key) {
  if (key == 0) {
    // the list on the right side can not be deleted
    return
  }

  // move elements from that list back to the main list
  for (var i = 0; i < lists.value[key].content.length; i++) {
    moveId = lists.value[key].content[i].id
    moveName = lists.value[key].content[i].name
    // no calcOp/calcVal/diagramVar
    lists.value[0].content.push({ id: moveId, name: moveName })
  }

  // delete elements in reverse order so that the keys are not regenerated
  for (var i = lists.value[key].content.length - 1; i >= 0; i--) {
    lists.value[key].content.splice(i, 1)
  }

  // delete the list
  lists.value.splice(key, 1)
}

function ddFilter(key) {
  if (search.value == prevSearch.value) {
    return null
  }

  clearTimeout(searchTimeout.value)

  searchTimeout.value = setTimeout(() => {
    axios.get(`${propData.routeConfigfileSearchDeviceParams}?search=${search.value}`)
    .then((response) => {
      lists.value[0].content = response.data
      prevSearch.value = search.value
    })
    .catch((error) => {
      snotify.error(error, 'Error:')
      console.log(error)
    })
  }, 300)
}

function refreshDeviceParams() {
  search.value = ''
  prevSearch.value = ''
  refresh.value = true

  axios.get(propData.routeConfigfileRefreshGenieAcs)
  .then((response) => {
    lists.value[0].content = response.data
    refresh.value = false
  })
  .catch((error) => {
    snotify.error(error, 'Error:')
    refresh.value = false
  })
}

async function refreshJson() {
  await nextTick()
  let json = {}
  let params = {}
  for (var key = 1; key < lists.value.length; key++) {
    var listName = lists.value[key].name
    params[listName] = {}
    for (var i = 0; i < lists.value[key].content.length; i++) {
      let content = lists.value[key].content[i]
      let calcOp = content.calcOp
      let calcVal = content.calcVal
      let diagramVar = content.diagramVar
      let diagramOp = content.diagramOp
      let diagramVal = content.diagramVal
      let colorize = content.colorize
      let colorDanger = content.colorDanger
      let colorWarning = content.colorWarning
      let colorSuccess = content.colorSuccess
      let valueType = content.valueType

      let calc = null
      if (calcOp !== null && calcVal !== null) {
        calc = [calcOp, calcVal]
      }

      let diagram = null
      if (diagramVar !== null && diagramOp !== null && diagramVal !== null) {
        diagram = [diagramVar, diagramOp, diagramVal]
      }

      let colors = null
      if (colorize) {
        colors = [colorize, colorDanger, colorWarning, colorSuccess, valueType]
      }

      params[listName][content.name] = [content.id, calc, diagram, colors]
      json[listName] = params[listName]
    }
  }

  // console.log(json)
  $('input[name=monitoring]')[0].value = JSON.stringify(json)
}

function blurInput(e) {
  e.target.blur()
}

function renameTitle(list) {
  list.name = list.name.replace(/^DT\_|^PT\_/g, '')

  if (list.type == 'table') {
    list.name = 'DT_' + list.name
  }

  if (list.type == 'paginated') {
    list.name = 'PT_' + list.name
  }
}

function setType(list) {
  if (list.name.startsWith('DT_')) {
    return (list.type = 'table')
  }

  if (list.name.startsWith('PT_')) {
    return (list.type = 'paginated')
  }

  list.type = 'list'
}
</script>
