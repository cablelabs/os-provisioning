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
  <div id="skeleton">
    <div class="flex justify-between">
      <div class="w-1/12">
        <div
          v-for="n in 6"
          :key="n"
          class="skeleton m-3 w-full rounded-sm"
        ></div>
      </div>
      <div class="skeleton rounded my-2 mx-3 w-1/3 h-48"></div>
    </div>

    <div>
      <div class="skeleton m-3 w-1/12 rounded-sm"></div>
      <div v-for="n in 6" :key="n" class="skeleton m-3 w-2/3 rounded-sm"></div>
    </div>
    <div>
      <div class="skeleton m-3 w-1/12 rounded-sm"></div>
      <div v-for="n in 6" :key="n" class="skeleton m-3 w-2/3 rounded-sm"></div>
    </div>
  </div>
</template>
