<template>
  <h2>{{ translations.dragDrop }}
    <a data-toggle="popover" data-html="true" data-container="body" data-trigger="hover" title="" data-placement="right"
      :data-content="translations.infotext"
      :data-original-title="translations.infoheader">
      <i class="fa fa-2x p-t-5 fa-question-circle text-info dragdropinfo"></i>
    </a>
  </h2>

  <div class="box" id="left">
    <draggable v-model="lists" :group="{ name: 'g1' }" class="droplist" :options="{draggable: '.list-group', filter: 'input', preventOnFilter: false}" @change="refreshSelect();refreshJson();">
      <template v-for="(list, key) in lists" :key="key">
        <div v-if="key != '0'" class="list-group">
          <div class="listbox" style="padding-bottom: 1.5rem;">
            <div class="h" style="display:flex;align-items:center;flex-wrap:wrap;">
              <input type="text" style="flex:1 auto;" v-model="list.name" @blur="setType(list);refreshSelectNextTick();refreshJson();" @keydown.enter.prevent='blurInput'>
              <div>
                <select2 name="listtype" v-model="list.type" v-dispatchsel2 @change="renameTitle(list);refreshJson();">
                  <option value="list">{{ translations.listtype.list }}</option>
                  <option value="table">{{ translations.listtype.table }}</option>
                  <option value="paginated">{{ translations.listtype.paginated }}</option>
                </select2>
              </div>
              <button class="btn btn-primary" @click="delList(key);refreshJson();">
                {{ translations.deleteList }}
              </button>
            </div>
            <draggable v-model="list.content" :group="{ name: 'g2' }" class="dropzone" :options="{draggable: '.dragdroplistitem', filter: 'input', preventOnFilter: false}" @change="refreshSelect();refreshJson();">
              <div class="dragdroplistitem" style="margin-bottom:.25rem;padding:.5rem;background-color: #f2f2f2;cursor: grabbing;" v-for="(item, id) in list.content" :key="item.id">
                <div class="d-flex flex-column" style="padding:.5rem;">
                  <div class="d-flex justify-content-between pb-2" :class="item.id">
                    <div style="font-weight: bold;word-break: break-all;" v-text="item.id"></div>
                    <i class="fa fa-cog dragdropitembutton pl-4" aria-hidden="true" @click="itemmenu($event.target, key, id)"></i>
                    <div class="dragdropitemmenubox">
                      <template v-for="(listname, listkey) in lists" :key="listkey">
                        <span class="dragdropitemmenubutton" v-if="listkey != '0' && listkey !=  key" @click="moveItem(key,listkey, id);refreshJson();">
                          {{ translations.moveTo }} {{ listname.name }}
                        </span>
                      </template>
                      <span class="dragdropitemmenubutton" @click="moveItem(key, -1, id);refreshJson();">
                        {{ translations.moveToNewList }}
                      </span>
                      <span class="dragdropitemmenubutton" @click="moveItem(key, 0, id);refreshJson();">
                        {{ translations.deleteElement }}
                      </span>
                    </div>
                  </div>
                  <div class="d-flex mb-2 align-items-center">
                    <div style="width:150px">{{ translations.displayName }}</div>
                    <input :placeholder="translations.displayNamePlaceholder" style="flex:1;" type="text" name="oname" v-model="item.name" @blur="refreshJson"/>
                  </div>
                  <div class="d-flex mb-2 align-items-center">
                    <div style="width:150px">{{ translations.analysisOperator }}</div>
                    <div style="flex:1;">
                      <select2 :data-placeholder="translations.operatorPlaceholder" name="calcOp" v-model="item.calcOp" v-dispatchsel2 @change="refreshJson">
                        <option value=""></option>
                        <option value="+">{{ translations.add }} (+)</option>
                        <option value="-">{{ translations.sustract }} (-)</option>
                        <option value="*">{{ translations.multiply }} (*)</option>
                        <option value="/">{{ translations.divide }} (/)</option>
                        <option value="%">{{ translations.modulo }} (%)</option>
                      </select2>
                    </div>
                  </div>
                  <div class="d-flex mb-2 align-items-center">
                    <div style="width:150px">{{ translations.analysisOperand }}</div>
                    <input style="flex:1;" :placeholder="translations.analysisOperandPlaceholder" type="number" step="0.0001" name="calcVal" v-model.number="item.calcVal" @blur="refreshJson"/>
                  </div>
                  <div>
                    <div class="d-flex mb-2 align-items-center">
                      <div style="width:150px">{{ translations.monitorInDiagram }}</div>
                      <div class="d-flex justify-content-center" style="flex:1;">
                        <input title="Monitor?" type="checkbox" class="toggleColorizeParams" name="colorize" v-model="item.monitorInDiagram">
                      </div>
                    </div>
                    <div v-show="item.monitorInDiagram">
                      <div class="d-flex mb-2 align-items-center">
                        <div style="width:150px">{{ translations.diagramColumn }}</div>
                        <div style="flex:1 1 100px;min-width:0;">
                          <select2 data-allow-clear="true" :data-placeholder="translations.diagramColumnPlaceholder" name="diagramVar" v-model="item.diagramVar" v-dispatchsel2 @change="refreshJson">
                            <option value=""></option>
                            <option v-for="(column, columnKey) in propDataColumns" :value="column" :key="columnKey">
                              {{ column }}
                            </option>
                          </select2>
                        </div>
                      </div>
                      <div class="d-flex mb-2 align-items-center">
                        <div style="width:150px">{{ translations.diagramOperator }}</div>
                        <div style="flex:1;">
                          <select2 data-allow-clear="true" :data-placeholder="translations.operatorPlaceholder" name="diagramOp" v-model="item.diagramOp" v-dispatchsel2 @change="refreshJson">
                            <option value=""></option>
                            <option value="+">{{ translations.add }} (+)</option>
                            <option value="-">{{ translations.sustract }} (-)</option>
                            <option value="*">{{ translations.multiply }} (*)</option>
                            <option value="/">{{ translations.divide }} (/)</option>
                            <option value="%">{{ translations.modulo }} (%)</option>
                          </select2>
                        </div>
                      </div>
                      <div class="d-flex mb-2 align-items-center">
                        <div style="width:150px">{{ translations.diagramOperand }}</div>
                        <input style="flex:1;" :placeholder="translations.diagramOperandPlaceholder" type="number" step="0.0001" name="diagramVal" v-model.number="item.diagramVal" @blur="refreshJson"/>
                      </div>
                    </div>
                  </div>

                  <div>
                    <div class="d-flex mb-2 align-items-center">
                      <div style="width:150px">{{ translations.colorize }}</div>
                      <div class="d-flex justify-content-center" style="flex:1;">
                        <input title="Colorize?" type="checkbox" class="toggleColorizeParams" name="colorize" v-model="item.colorize" @change="refreshJson">
                      </div>
                    </div>
                    <div v-show="item.colorize" class="d-flex flex-column">
                      <input type="text" name="colorDanger" style="background-color: #ffddbb;margin-top:.5rem;" :placeholder="propData.configfileDragDropThreshholdsCriticalOrange" :title="propData.configfileDragDropThreshholdsCriticalOrange" v-model="item.colorDanger" @blur="refreshJson"/>
                      <input type="text" name="colorWarning" style="background-color: #ffffdd;margin-top:.5rem;" :placeholder="propData.configfileDragDropThreshholdsWarningYellow" :title="propData.configfileDragDropThreshholdsWarningYellow" v-model="item.colorWarning" @blur="refreshJson"/>
                      <input type="text" name="colorSuccess" style="background-color: #ddffdd;margin-top:.5rem;" :placeholder="propData.configfileDragDropThreshholdsSuccessGreen" :title="propData.configfileDragDropThreshholdsSuccessGreen" v-model="item.colorSuccess" @blur="refreshJson"/>
                      <select2 data-allow-clear="true" :data-placeholder="ConfigfileDragDropSelectMapParameter" style="margin-top:.5rem;width:auto;" name="valueType" v-model="item.valueType" title="Usage e.g. in topo map" v-dispatchsel2 @change="refreshJson">
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
            </draggable>
          </div>
        </div>
      </template>
    </draggable>

    <div class="newlist">
      <input type="text" v-model="listName" :placeholder="translations.listname" @keydown.enter.prevent="addList();refreshJson();" />
      <button class="btn btn-primary" @click.prevent="addList();refreshJson();">
        {{ translations.addlist }}
      </button>
    </div>
  </div>

  <div class="box" id="right">
    <div :group="{ name: 'g1' }" class="droplist" >
      <template v-for="(list, key) in lists" :key="key">
        <div v-if="key == '0'" class="list-group">
          <div class="listbox">
            <div class="h d-flex align-items-center">
              <div class="pr-4">{{ translations.deviceParameters }}</div>
              <a :href="propData.routeConfigfileRefreshGenieAcs" class="btn btn-primary">
                {{ translations.refresh }}
              </a>
            </div>
            <input class="mb-3 w-100" type="text" @keyup.prevent="ddFilter" @keydown.enter.prevent='blurInput' v-model="search" :placeholder="propData.buttonSearch"/>
            <draggable v-model="list.content" :group="{ name: 'g2' }" class="dropzone" :options="{draggable: '.dragdroplistitem', filter: 'input', preventOnFilter: false}">
              <div class="dragdroplistitem" style="margin-bottom:.25rem;padding:.5rem;background-color: #f2f2f2;cursor: grabbing;" v-for="(item, id) in list.content" :key="item.id">
                <div>
                  <div class="d-flex justify-content-between pb-2" :class="item.id">
                    <div style="font-weight: bold;word-break: break-all;" v-text="item.id"></div>
                    <i class="fa fa-cog dragdropitembutton pl-4" aria-hidden="true" @click="itemmenu($event.target, key, id)"></i>
                    <div class="dragdropitemmenubox">
                      <template v-for="(listname, listkey) in lists" :key="listkey">
                        <span class="dragdropitemmenubutton" v-if="listkey != '0'" @click="moveItem(key,listkey, id)">
                          {{ translations.moveTo }} {{ listname.name }}
                        </span>
                      </template>
                      <span class="dragdropitemmenubutton" @click="moveItem(key,-1, id)">{{ translations.moveToNewList }}</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center">
                    <div style="margin-right:.5rem;">{{ translations.displayName }}</div>
                    <input style="flex:1;" type="text" name="oname" v-model="item.name"/>
                  </div>
                </div>
              </div>
            </draggable>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'

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

// mounted
onMounted(() => {
  for (let key = 1; key < lists.value.length; key++) {
  for (let i = 0; i < lists.value[key].content.length; i++) {
    let content = lists.value[key].content[i];
    content.monitorInDiagram = Boolean(content.diagramVar || content.diagramOp || content.diagramVal)
  }
  }
})

function itemmenu(element, key, id) {
  let targetElement = element.parentNode.getElementsByClassName("dragdropitemmenubox")[0];
  if (targetElement.style.display != "block") {
  targetElement.style.display = "block";
  } else {
  targetElement.style.display = "none";
  }
}

async function moveItem(olist, key, id) {
  // for creating a new list
  if (key == -1) {
  lists.value.push({
    name: translations.listname,
    content: []
  });
  key = lists.value.length-1;
  }
  // move item
  moveId = lists.value[olist].content[id].id;
  moveName = lists.value[olist].content[id].name;
  moveCalcOp = '';
  moveCalcVal = '';
  moveDiagramVar = '';
  moveDiagramOp = '';
  moveDiagramVal = '';
  moveColorize = '';
  moveColorDanger = '';
  moveColorWarning = '';
  moveColorSuccess = '';
  lists.value[key].content.push({
    'id': moveId,
    'name': moveName,
    'calcOp': moveCalcOp,
    'calcVal': moveCalcVal,
    'diagramVar': moveDiagramVar,
    'diagramOp': moveDiagramOp,
    'diagramVal': moveDiagramVal,
    'colorize': moveColorize,
    'colorDanger': moveColorDanger,
    'colorWarning': moveColorWarning,
    'colorSuccess': moveColorSuccess,
    'valueType': moveColorSuccess
  });
  lists.value[olist].content.splice(id, 1);
  await nextTick()
  refreshSelect()
}

function addList() {
  if (! listName.value) {
  return;
  }

  lists.value.push({
  name: listName.value,
  content: []
  });

  listName.value = '';
}

function delList(key) {
  if (key == 0) {
  // the list on the right side can not be deleted
  return;
  }

  // move elements from that list back to the main list
  for (var i=0;i < lists.value[key].content.length; i++) {
  moveId = lists.value[key].content[i].id;
  moveName = lists.value[key].content[i].name;
  // no calcOp/calcVal/diagramVar
  lists.value[0].content.push({'id': moveId, 'name': moveName});
  }

  // delete elements in reverse order so that the keys are not regenerated
  for (var i = lists.value[key].content.length-1; i >= 0; i--) {
  lists.value[key].content.splice(i, 1);
  }

  // delete the list
  lists.value.splice(key, 1);
}

function ddFilter(key) {
  if (search.value == prevSearch.value) {
  return null;
  }

  clearTimeout(searchTimeout.value)

  searchTimeout.value = setTimeout(() => {
  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4) {
    lists.value[0].content = JSON.parse(this.responseText);
    prevSearch.value = search.value;
    }
  };
  xhttp.open('GET', `${propData.routeConfigfileSearchDeviceParams}?search=${search.value}`, true);
  xhttp.send();
  }, 300);

}

function refreshSelect() {
  $('select').select2()
}

async function refreshSelectNextTick() {
  await nextTick()
  refreshSelect()
}

async function refreshJson() {
  await nextTick();
  let json = {};
  let params = {};
  for (var key = 1; key < lists.value.length; key++) {
  var listName = lists.value[key].name;
  params[listName] = {};
  for (var i = 0; i < lists.value[key].content.length; i++) {
    let content = lists.value[key].content[i];
    let calcOp = content.calcOp;
    let calcVal = content.calcVal;
    let diagramVar = content.diagramVar;
    let diagramOp = content.diagramOp;
    let diagramVal = content.diagramVal;
    let colorize = content.colorize;
    let colorDanger = content.colorDanger;
    let colorWarning = content.colorWarning;
    let colorSuccess = content.colorSuccess;
    let valueType = content.valueType;

    let calc = null
    if (calcOp !== null && calcVal !== null) {
    calc = [calcOp, calcVal];
    }

    let diagram = null
    if (diagramVar !== null && diagramOp !== null && diagramVal !== null) {
    diagram = [diagramVar, diagramOp, diagramVal];
    }

    let colors = null
    if (colorize) {
    colors = [colorize, colorDanger, colorWarning, colorSuccess, valueType]
    }

    params[listName][content.name] = [content.id, calc, diagram, colors];
    json[listName] = params[listName];
  }
  }

  $('input[name=monitoring]')[0].value = JSON.stringify(json);
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
  return list.type = 'table'
  }

  if (list.name.startsWith('PT_')) {
  return list.type = 'paginated'
  }

  list.type = 'list'
}
</script>
