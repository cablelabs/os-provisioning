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
	<img class="object-cover" :src="`/images/inventory/front/${baseChassis.image}.jpg`" alt="front" />
	<div class="absolute top-4 w-92 ml-3">
        <div class="w-full relative">
            <template v-for="(lineCard, index) in topLineCard" :key="index">
                <template v-if="lineCard.name">
                    <img class="object-cover h-9 pb-px w-full" :src="`/images/inventory/front/${lineCard.image}.png`"/>
                    <div v-if="getLineCardPhyModules(lineCard) && getLineCardPhyModules(lineCard).length > 0" class="absolute top-0 right-0">
                        <img v-for="(phyModule, index) in getLineCardPhyModules(lineCard)" class="object-cover h-4 w-full pb-1" :src="`/images/inventory/front/${phyModule.image}.jpeg`" :key="index" />
                    </div>
                </template>
                <div v-else class="h-9 pb-px w-full"></div>
            </template>
        </div>
        <div class="w-full">
            <div v-for="(supervisorCard, index) in middleSupervisorCard" class="relative" :key="index">
                <img class="object-cover h-16 pb-px w-full" :src="`/images/inventory/front/${supervisorCard.image}.png`"/>
                <div v-if="getSFPModule(supervisorCard) && getSFPModule(supervisorCard).length > 0" class="absolute top-0 left-0 flex flex-wrap w-45">
                    <template v-for="(sfpModule, index) in getSFPModule(supervisorCard)" :key="index">
                        <img class="object-cover h-4" :src="`/images/inventory/front/${sfpModule.image}.jpeg`" />
                    </template>
                </div>
                 <div v-if="getQSFPModule(supervisorCard)" class="absolute top-8 left-20 mt-2">
                    <img class="object-cover h-4 w-full" :src="`/images/inventory/front/${getQSFPModule(supervisorCard).image}.jpg`"/>
                </div>
            </div>
        </div>
       <div class="w-full">
            <template v-for="(lineCard, index) in bottomLineCard" :key="index">
                <img v-if="lineCard.name" class="object-cover h-9 pb-px w-full" :src="`/images/inventory/front/${lineCard.image}.png`"/>
                <div v-else class="h-9 pb-px w-full"></div>
            </template>
        </div>
        <div class="pt-3">
            <div class="flex flex-wrap mt-2.5">
                <template v-for="(powerModule, index) in powerSupply" :key="index">
                    <img class="object-cover w-1/3" :src="`/images/inventory/front/${powerModule.image}.jpeg`" />
                </template>
            </div>
        </div>
	</div>
</template>

<script setup>
import { inventoryFrontList } from './inventoryService.js'
import _ from 'lodash'
import { computed } from 'vue'

const backendData = [
	{
        "name": "Chassis",
		"pid": "CBR-8-CCAP-CHASS"
	},
    {
        "name": "ATO clc 1",
		"pid": "No-LEOBEN-INSTALLED"
	},
    {
        "name": "digi-pic 0/1",
		"pid": "CBR-DPIC-8X10G"
	},
    {
        "name": "digi-pic 1/1",
		"pid": "CBR-DPIC-8X10G"
	},
    {
        "name": "digi-pic 2/1",
		"pid": "CBR-DPIC-8X10G"
	},
    {
        "name": "digi-pic 3/1",
		"pid": "CBR-DPIC-8X10G"
	},
    {
        "name": "SFP+ module 0/1/0",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 0/1/1",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 0/1/2",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 0/1/3",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 0/1/4",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 0/1/5",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 0/1/6",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 0/1/7",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 1/1/0",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 1/1/1",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 1/1/2",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 1/1/3",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 1/1/4",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 1/1/5",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 1/1/6",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 1/1/7",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 2/1/0",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 2/1/1",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 2/1/2",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 2/1/3",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 2/1/4",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 2/1/5",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 2/1/6",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 2/1/7",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 3/1/0",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 3/1/1",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 3/1/2",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 3/1/3",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 3/1/4",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 3/1/5",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 3/1/6",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "SFP+ module 3/1/7",
		"pid": "SFP+ 10GBASE-LR"
	},
    {
        "name": "CLC Downstream PHY Module 1/0",
		"pid": "CBR-D31-DS-MOD"
	},
    {
        "name": "CLC Downstream PHY Module 1/1",
		"pid": "CBR-D31-DS-MOD"
	},
    {
        "name": "sup-pic 4/1",
		"pid": "CBR-2X100G-PIC"
	},
    {
        "name": "sup-pic 5/1",
		"pid": "CBR-2X100G-PIC"
	},
    {
        "name": "Power Supply Module 0",
		"pid": "CBR-AC-PS"
	},
    {
        "name": "Power Supply Module 1",
		"pid": "CBR-AC-PS"
	},
    {
        "name": "Power Supply Module 2",
		"pid": "CBR-AC-PS"
	},
    {
        "name": "Power Supply Module 3",
		"pid": "CBR-AC-PS"
	},
    {
        "name": "Power Supply Module 4",
		"pid": "CBR-AC-PS"
	},
    {
        "name": "QSFP module 4/1 port0",
		"pid": "QSFP 100GE LR4"
	},
    {
        "name": "QSFP module 5/1 port0",
		"pid": "QSFP 100GE LR4"
	},
];

const mappedBackendData = computed(() => {
    return _.map(inventoryFrontList, (data, key) => {
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

const topLineCard = computed(() => {
  return _.chain(mappedBackendData.value).filter((device) => device.side === 'top')
            .orderBy((device) => device.position, 'asc')
			.value();
});

const middleSupervisorCard = computed(() => {
  return _.chain(mappedBackendData.value).filter((device) => device.side === 'middle')
            .orderBy((device) => device.position, 'asc')
			.value();
});

const bottomLineCard = computed(() => {
  return _.chain(mappedBackendData.value).filter((device) => (device.side === 'bottom' && !device.inside))
            .orderBy((device) => device.position, 'asc')
			.value();
});

const powerSupply = computed(() => {
  return _.chain(mappedBackendData.value).filter((device) => device.side === 'full-bottom' && device.position !== null)
            .orderBy((device) => device.position, 'asc')
			.value();
});

function getLineCardPhyModules(lineCard) {
    return _.filter(mappedBackendData.value, (device) => device.inside === `${lineCard.name}:${lineCard.pid}`)
}

function getSFPModule(supervisorCard) {
    return _.filter(mappedBackendData.value, (device) => device.inside === `${supervisorCard.name}:${supervisorCard.pid}` && device.side === null)
}

function getQSFPModule(supervisorCard) {
    console.log(_.find(mappedBackendData.value, (device) => (device.inside === `${supervisorCard.name}:${supervisorCard.pid}` && device.side === 'bottom')))
    console.log(_.find(mappedBackendData.value, (device) => (device.inside === `${supervisorCard.name}:${supervisorCard.pid}`)))
    return _.find(mappedBackendData.value, (device) => (device.inside === `${supervisorCard.name}:${supervisorCard.pid}` && device.side === 'bottom'))
}
</script>
<style>
.w-92 {
	width: 92%;
}
.w-45 {
    width: 45%;
}
</style>
