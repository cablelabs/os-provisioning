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
		<img v-for="(device, index) in rightSideDevices" :key="index" class="object-cover" :src="`/images/inventory/${device.type}.png`" />
	</div>
	<div class="absolute bottom-0">
		<img v-for="(device, index) in bottomDevices" :key="index" class="object-cover" :src="`/images/inventory/${device.type}.png`" />
	</div>
	<div class="absolute left-3 top-0">
		<img v-for="(device, index) in leftSideDevices" :key="index" class="object-cover" :src="`/images/inventory/${device.type}.png`" />
	</div>
</template>

<script setup>
import _ from 'lodash'
import { ref, computed, onMounted, onDeactivated, nextTick } from 'vue'

const backendData = [
	{
		"type": "CBR-8-CCAP-CHASS",
		"position": null, // null means base chassis
		"side": null // null means base chassis
	},
	{
		"type": "CBR-CCAP-LC-40G",
		"position": 0,
		"side": "left" // left | right | full
	},
	{
		"type": "CBR-CCAP-LC-40G",
		"position": 1,
		"side": "left" // left | right | full
	},
	{
		"type": "CBR-CCAP-LC-40G",
		"position": 2,
		"side": "left" // left | right | full
	},
	{
		"type": "CBR-CCAP-LC-40G",
		"position": 3,
		"side": "left" // left | right | full
	},
	{
		"type": "CBR-CCAP-LC-40G",
		"position": 6,
		"side": "left" // left | right | full
	},
	{
		"type": "CBR-CCAP-LC-40G",
		"position": 7,
		"side": "left" // left | right | full
	},
	{
		"type": "CBR-CCAP-LC-40G",
		"position": 8,
		"side": "left" // left | right | full
	},
	{
		"type": "CBR-CCAP-SUP-250G",
		"position": 4,
		"side": "left" // left | right | full
	},
	{
		"type": "CBR-CCAP-SUP-250G",
		"position": 5,
		"side": "left" // left | right | full
	},
	{
		"type": "CBR-PEM-AC-6M",
		"position": 0,
		"side": "full" // left | right | full
	},
	{
		"type": "CBR-PEM-AC-6M",
		"position": 1,
		"side": "full" // left | right | full
	},
	{
		"type": "CBR-FAN-ASSEMBLY",
		"position": 0,
		"side": "right" // left | right | full
	},
	{
		"type": "CBR-FAN-ASSEMBLY",
		"position": 1,
		"side": "right" // left | right | full
	},
	{
		"type": "CBR-FAN-ASSEMBLY",
		"position": 2,
		"side": "right" // left | right | full
	},
	{
		"type": "CBR-FAN-ASSEMBLY",
		"position": 3,
		"side": "right" // left | right | full
	},
	{
		"type": "CBR-FAN-ASSEMBLY",
		"position": 4,
		"side": "right" // left | right | full
	},
];

const baseChassis = computed(() => {
  return _.find(backendData, (device) => device.position === null && device.side === null)
});

const leftSideDevices = computed(() => {
  return _.chain(backendData).filter((device) => device.side === 'left')
  					.orderBy((device) => device.position, 'asc')
					.value();
});

const rightSideDevices = computed(() => {
  return _.chain(backendData).filter((device) => device.side === 'right')
  					.orderBy((device) => device.position, 'asc')
					.value();
});

const bottomDevices = computed(() => {
  return _.chain(backendData).filter((device) => device.side === 'full')
  					.orderBy((device) => device.position, 'asc')
					.value();
});
</script>
