/** * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version") * and others
– powered by CableLabs. All rights reserved. * * Licensed under the Apache
License, Version 2.0 (the "License"); * you may not use this file except in
compliance with the License. * You may obtain a copy of the License at: * *
http://www.apache.org/licenses/LICENSE-2.0 * * Unless required by applicable law
or agreed to in writing, software * distributed under the License is distributed
on an "AS IS" BASIS, * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
express or implied. * See the License for the specific language governing
permissions and * limitations under the License. */

<script setup>
import { ref, reactive, onMounted, toRefs } from 'vue'

let propData = document.querySelector('#auth-abilities').dataset

const allowAll = ref(undefined)
const allowAllId = ref(1)
const allowViewAll = ref(undefined)
const allowViewAllId = ref(2)
const loadingSpinner = reactive({})
const spinner = ref(false)
const changed = ref([])
const showSaveColumn = ref(false)
const showCapabilitySaveColumn = ref(false)
const capabilities = ref(JSON.parse(propData.capabilities))
const originalCapabilities = ref(JSON.parse(propData.capabilities))
const customAbilities = ref(JSON.parse(propData.customAbilities))
const roleAbilities = ref(JSON.parse(propData.roleAbilities))
const originalRoleAbilities = ref(JSON.parse(propData.roleAbilities))
const roleForbiddenAbilities = ref(JSON.parse(propData.roleForbiddenAbilities))
const originalForbiddenAbilities = ref(
  JSON.parse(propData.roleForbiddenAbilities)
)
const modelAbilities = ref(JSON.parse(propData.modelAbilities))
const originalModelAbilities = ref(JSON.parse(propData.modelAbilities))
const permissions = reactive({
  view: {},
  create: {},
  update: {},
  delete: {},
  manage: {},
  save: {}
})

const button = reactive({
  allow: propData.abilityAllowTo,
  forbid: propData.abilityForbidTo
})

// mounted
onMounted(() => {
  setupCustomAbilities()
  setupModelAbilities()
})

// methods
function setupCustomAbilities() {
  for (id in customAbilities.value) {
    if (customAbilities.value[id]['title'] == 'All abilities')
      allowAllId.value = id

    if (customAbilities.value[id]['title'] == 'View everything')
      allowViewAllId.value = id
  }

  for (id in customAbilities.value) {
    if (id in originalRoleAbilities.value) {
      document.getElementById(`allowed${id}`).checked = true
      if (id == allowAllId.value) allowAll.value = true
      if (id == allowViewAllId.value) allowViewAll.value = true
    }

    if (id in originalForbiddenAbilities.value) {
      document.getElementById(`forbidden${id}`).checked = true
      if (id == allowAllId.value) allowAll.value = false
      if (id == allowViewAllId.value) allowViewAll.value = false
    }

    changed.value[id] = false
  }

  loadingSpinner.custom = false
}

function setupModelAbilities() {
  for (let module in modelAbilities.value) {
    permissions.manage[module] = checkShortcutButtons('*', module)
    permissions.view[module] = checkShortcutButtons('view', module)
    permissions.create[module] = checkShortcutButtons('create', module)
    permissions.update[module] = checkShortcutButtons('update', module)
    permissions.delete[module] = checkShortcutButtons('delete', module)
    permissions.save[module] = checkShortcutButtons('save', module)
    loadingSpinner[module] = false
  }
}

function checkForbiddenVisibility(id) {
  if (id == allowViewAllId.value || id == allowAllId.value) return false

  return (
    (allowAll.value && id != allowAllId.value) || allowAll.value == undefined
  )
}

function checkChangedArray(array) {
  return array.includes(true) ? true : false
}

function hasChanged(id) {
  if (document.getElementById(`allowed${id}`).checked)
    return id in originalRoleAbilities.value ? false : true

  if (document.getElementById(`forbidden${id}`).checked)
    return id in originalForbiddenAbilities.value ? false : true

  if (
    !document.getElementById(`allowed${id}`).checked ||
    !document.getElementById(`forbidden${id}`).checked
  )
    return id in originalRoleAbilities.value ||
      id in originalForbiddenAbilities.value
      ? true
      : false
}

function customAllow(id) {
  if (document.getElementById(`allowed${id}`).checked) {
    if (id == allowAllId.value) {
      allowAll.value = true
      allowViewAll.value = undefined
      document.getElementById(`allowed${allowViewAllId.value}`).checked = false
      changed.value.splice(
        allowViewAllId.value,
        1,
        hasChanged(allowViewAllId.value)
      )
      delete roleAbilities.value[allowViewAllId.value]
    }

    allowViewAll.value = id == allowViewAllId.value ? true : allowViewAll.value
    roleAbilities.value[id] = customAbilities.value[id]['localTitle']
    delete roleForbiddenAbilities.value[id]
  } else {
    if (id == allowAllId.value) {
      allowAll.value = undefined
      changed.value.splice(
        allowViewAllId.value,
        1,
        hasChanged(allowViewAllId.value)
      )
    }

    allowViewAll.value =
      id == allowViewAllId.value ? undefined : allowViewAll.value
    delete roleAbilities.value[id]
  }

  document.getElementById(`forbidden${id}`).checked = false
  changed.value.splice(id, 1, hasChanged(id))
  showSaveColumn.value = checkChangedArray(changed.value)
}

function customForbid(id) {
  if (document.getElementById(`forbidden${id}`).checked) {
    roleForbiddenAbilities.value[id] = customAbilities.value[id]['localTitle']
    delete roleAbilities.value[id]
  } else {
    delete roleForbiddenAbilities.value[id]
  }

  document.getElementById(`allowed${id}`).checked = false
  changed.value.splice(id, 1, hasChanged(id))
  showSaveColumn.value = checkChangedArray(changed.value)
}

function changeModelAbility(event) {
  let name = event.target.id.split('_')
  let action = name[0]
  let actionShortcut = action == 'manage' ? '*' : action
  let module = name[1]

  if (!event.target.checked) permissions[action][module] = false
  else {
    permissions[action][module] = checkShortcutButtons(actionShortcut, module)
  }
}

function showInput(elementId) {
  return !document.getElementById(elementId).checked
}

function saveButton(module) {
  if (
    _.isEqual(
      modelAbilities.value[module],
      originalModelAbilities.value[module]
    )
  )
    return false

  return true
}

function shortcutButtonClick(event) {
  let module = event.target.name.split('_')
  setShortcutButtons(module[0], module[1])
}

function setShortcutButtons(action, module) {
  let actionShortcut = action == 'manage' ? '*' : action

  if (!permissions[action][module]) {
    permissions[action][module] = true
    for (let model in modelAbilities.value[module]) {
      let set = new Set(modelAbilities.value[module][model])
      set.add(actionShortcut)
      modelAbilities.value[module][model] = Array.from(set)
    }
  } else {
    permissions[action][module] = false
    for (let model in modelAbilities.value[module]) {
      if (actionShortcut == '*')
        document.getElementById(
          'manage' + '_' + module + '_' + model
        ).checked = false
      let set = new Set(modelAbilities.value[module][model])
      set.delete(actionShortcut)
      modelAbilities.value[module][model] = Array.from(set)
    }
  }
}

function checkShortcutButtons(actionShortcut, module) {
  let check = true

  for (let model in modelAbilities.value[module]) {
    let set = new Set(modelAbilities.value[module][model])
    check = check && set.has(actionShortcut)
    modelAbilities.value[module][model] = Array.from(set)
  }

  return check
}

function capabilityChange(id) {
  capabilities.value[id].isCapable = !capabilities.value[id].isCapable
  showCapabilitySaveColumn.value = checkChangedArray(
    Object.keys(capabilities.value).map(
      (key) => capabilities.value[key].isCapable
    )
  )
}

function capabilityUpdate(id) {
  let token = document.querySelector('input[name="_token"]').value

  loadingSpinner.capabilities = true
  loadingSpinner = _.clone(loadingSpinner)

  axios({
    method: 'post',
    url: propData.routeCapabilityUpdate,
    headers: { 'X-CSRF-TOKEN': token },
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    data: {
      id: id,
      capabilities: capabilities.value,
      roleId: propData.viewVarId
    }
  })
    .then(function (response) {
      originalCapabilities.value = response.data.capabilities

      loadingSpinner.capabilities = false
      showCapabilitySaveColumn.value = checkChangedArray(changed.value)
    })
    .catch(function (error) {
      alert(error)
    })
}

function customUpdate(id) {
  let token = document.querySelector('input[name="_token"]').value

  loadingSpinner.custom = true
  loadingSpinner = _.clone(loadingSpinner)

  axios({
    method: 'post',
    url: propData.routeCustomAbilityUpdate,
    headers: { 'X-CSRF-TOKEN': token },
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    data: {
      id: id,
      roleAbilities: roleAbilities.value,
      roleForbiddenAbilities: roleForbiddenAbilities.value,
      changed: changed.value,
      roleId: propData.viewVarId
    }
  })
    .then(function (response) {
      originalRoleAbilities.value = response.data.roleAbilities
      originalForbiddenAbilities.value = response.data.roleForbiddenAbilities

      if (changed.value[allowAllId.value]) {
        for (module in modelAbilities.value) {
          modelUpdate(module)
        }
      }

      if (typeof response.data.id === 'object') {
        for (let id in response.data.id) {
          changed.value.splice(
            response.data.id[id],
            1,
            hasChanged(response.data.id[id])
          )
        }
      } else {
        changed.value.splice(response.data.id, 1, hasChanged(response.data.id))
      }

      loadingSpinner.custom = false
      showSaveColumn.value = checkChangedArray(changed.value)
    })
    .catch(function (error) {
      alert(error)
    })
}

function modelUpdate(module) {
  loadingSpinner[module] = true
  loadingSpinner = _.clone(loadingSpinner) // let watcher know something changed
  let form = document.getElementById(module)
  let formData = new FormData(form)

  formData.append('roleId', propData.viewVarId)
  formData.append('allowAll', allowAll.value)
  formData.append('module', module)

  axios({
    method: 'post',
    url: propData.routeModelAbilityUpdate,
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    data: formData
  })
    .then(function (response) {
      originalModelAbilities.value = response.data
      modelAbilities.value[module] = _.clone(response.data[module])
      loadingSpinner[module] = false
    })
    .catch(function (error) {
      alert(error)
    })
}
</script>
