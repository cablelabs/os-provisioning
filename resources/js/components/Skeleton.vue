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
