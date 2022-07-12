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
	<img class="object-cover w-full" :src="`/images/inventory/${baseChassis.image}.png`" alt="rear" />
	<div class="absolute top-0 w-full">
		<div class="flex w-full">
			<div class="w-2/3">
				<template v-for="(device, index) in leftSideDevices" :key="index">
					<img v-if="device.name" class="object-cover w-full" :src="`/images/inventory/${device.image}.png`" />
					<div v-else class="h-10"></div>
				</template>
			</div>
			<div class="w-1/3">
				<template v-for="(device, index) in rightSideDevices" :key="index">
					<img v-if="device.name" class="object-cover h-24 w-full" :src="`/images/inventory/${device.image}.png`" />
					<div v-else class="h-24"></div>
				</template>
			</div>
		</div>
		<div class="w-full">
			<template v-for="(device, index) in bottomDevices" :key="index">
				<img v-if="device.name" class="object-cover h-16 w-full" :src="`/images/inventory/${device.image}.png`" />
				<div v-else class="h-16"></div>
			</template>
		</div>
	</div>
	
</template>

<script setup>
import { inventoryRearList } from './inventoryService.js'
import _ from 'lodash'
import { computed } from 'vue'

const backendData = [
	{
        "name": "Chassis",
		"pid": "CBR-8-CCAP-CHASS"
	},
	{
        "name": "clc 0",
		"pid": "CBR-CCAP-LC-40G-R"
	},
	{
        "name": "clc 3",
		"pid": "CBR-CCAP-LC-40G-R"
	},
	{
        "name": "ATO clc 0",
		"pid": "CBR-CCAP-LC-40G-R"
	},
	{
        "name": "ATO clc 2",
		"pid": "CBR-CCAP-LC-40G-R"
	},
	{
        "name": "ATO clc 3",
		"pid": "CBR-CCAP-LC-40G-R"
	},
	{
        "name": "ATO sup 0",
		"pid": "CBR-CCAP-SUP-250G"
	},
	{
        "name": "ATO sup 1",
		"pid": "CBR-CCAP-SUP-250G"
	},
	{
        "name": "Power Shelf Module P0",
		"pid": "CBR-PEM-AC-6M"
	},
	{
        "name": "Power Shelf Module P1",
		"pid": "CBR-PEM-AC-6M"
	},
	{
        "name": "Fan Tray 0",
		"pid": "CBR-FAN-ASSEMBLY"
	},
	{
        "name": "Fan Tray 1",
		"pid": "CBR-FAN-ASSEMBLY"
	},
	{
        "name": "Fan Tray 2",
		"pid": "CBR-FAN-ASSEMBLY"
	},
	{
        "name": "Fan Tray 3",
		"pid": "CBR-FAN-ASSEMBLY"
	},
	{
        "name": "Fan Tray 4",
		"pid": "CBR-FAN-ASSEMBLY"
	},
];

const mappedBackendData = computed(() => {
    return _.map(inventoryRearList, (data, key) => {
			const [name, pid] = key.split(":");
			const deviceDetail = _.find(backendData, (device) => device.name === name && device.pid === pid);
		return {
			...data,
			...deviceDetail
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
<style>
.bottom-230 {
	bottom: 282px;
}
</style>
