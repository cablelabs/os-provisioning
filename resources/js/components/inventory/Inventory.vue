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
	<div class="flex">
		<div> 
      <div class="text-lg font-bold text-center mb-6">Front View</div>
      <div class="relative max-width-content h-screen"><FrontView /></div>
    </div>
    <div>
      <div class="text-lg font-bold text-center mb-6">Rear View</div>
		  <div class="relative max-width-content h-screen ml-16"><RearView /></div>
    </div>
	</div>
</template>

<script setup>
import FrontView from './FrontView'
import RearView from './RearView'
</script>

<style>
	.bottom-0 {
      bottom: 0;
    }
    .max-width-content{
      max-width: max-content;
    }
</style>