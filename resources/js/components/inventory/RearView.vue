/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

<template>
	<img class="object-cover" :src="`/images/inventory/${baseChassis.type}.png`" alt="rear" />
	<div class="absolute top-0 right-0">
		<img v-for="(device, index) in rightSideDevices" :key="index" class="object-cover" :src="`/images/inventory/${device.image}.png`" />
	</div>
	<div class="absolute bottom-0">
		<img v-for="(device, index) in bottomDevices" :key="index" class="object-cover" :src="`/images/inventory/${device.image}.png`" />
	</div>
	<div class="absolute left-3 top-0">
		<img v-for="(device, index) in leftSideDevices" :key="index" class="object-cover" :src="`/images/inventory/${device.image}.png`" />
	</div>
</template>

<script setup>
import { inventoryList } from './inventoryService.js'
import _ from 'lodash'
import { ref, computed, onMounted, onDeactivated, nextTick } from 'vue'

const backendData = [
	{
        "name": "Chassis",
		"type": "CBR-8-CCAP-CHASS"
	},
	{
        "name": "clc 0",
		"type": "CBR-CCAP-LC-40G-R"
	},
	{
        "name": "clc 1",
		"type": "CBR-CCAP-LC-40G"
	},
	{
        "name": "clc 2",
		"type": "CBR-CCAP-LC-40G-R"
	},
	{
        "name": "clc 3",
		"type": "CBR-CCAP-LC-40G-R"
	},
	{
        "name": "ATO clc 0",
		"type": "CBR-CCAP-LC-40G-R"
	},
	{
        "name": "ATO clc 2",
		"type": "CBR-CCAP-LC-40G-R"
	},
	{
        "name": "ATO clc 3",
		"type": "CBR-CCAP-LC-40G-R"
	},
	{
        "name": "ATO sup 0",
		"type": "CBR-CCAP-SUP-250G"
	},
	{
        "name": "ATO sup 1",
		"type": "CBR-CCAP-SUP-250G"
	},
	{
        "name": "Power Shelf Module P0",
		"type": "CBR-PEM-AC-6M"
	},
	{
        "name": "Power Shelf Module P1",
		"type": "CBR-PEM-AC-6M"
	},
	{
        "name": "Fan Tray 0",
		"type": "CBR-FAN-ASSEMBLY"
	},
	{
        "name": "Fan Tray 1",
		"type": "CBR-FAN-ASSEMBLY"
	},
	{
        "name": "Fan Tray 2",
		"type": "CBR-FAN-ASSEMBLY"
	},
	{
        "name": "Fan Tray 3",
		"type": "CBR-FAN-ASSEMBLY"
	},
	{
        "name": "Fan Tray 4",
		"type": "CBR-FAN-ASSEMBLY"
	},
];

const mappedBackendData = computed(() => {
    return _.map(backendData, (data) => {
        return {
            ...data,
            ...inventoryList[`${data.name}:${data.type}`]
        }
    });
});

const baseChassis = computed(() => {
  return _.find(mappedBackendData.value, (device) => device.position === null && device.side === null)
});

const leftSideDevices = computed(() => {
  return _.chain(mappedBackendData.value).filter((device) => device.side === 'left')
  					.orderBy((device) => device.position, 'asc')
					.value();
});

const rightSideDevices = computed(() => {
  return _.chain(mappedBackendData.value).filter((device) => device.side === 'right')
  					.orderBy((device) => device.position, 'asc')
					.value();
});

const bottomDevices = computed(() => {
  return _.chain(mappedBackendData.value).filter((device) => device.side === 'full')
  					.orderBy((device) => device.position, 'asc')
					.value();
});
</script>
