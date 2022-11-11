<div v-cloak id="sidebar-right" class="fixed right-0 flex h-full text-gray-100">
    <div class="relative w-64 mt-12 transition-all duration-200 bg-stone-800"
        :class="{ 'translate-x-0': !store.minifiedRight, 'translate-x-full': store.minifiedRight }">
        <div class="absolute flex flex-col items-center w-5 h-full pt-2 space-y-4 -left-5 bg-lime-nmsprime">
            <div class="hover:cursor-pointer" v-on:click="minifySidebarRight">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 duration-300 ease-in-out" :class="{ 'rotate-180': store.minifiedRight, 'rotate-0': !store.minifiedRight }" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <div v-if="!store.minifiedRight" class="hover:cursor-pointer" v-on:click="pinSidebarRight">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 duration-300 ease-in-out" :class="{ 'block rotate-0': pinned, 'block -rotate-90 hover:rotate-0': !pinned }" fill="currentColor" viewBox="0 0 384 512"
                    stroke="none" stroke-width="2">
                    <path
                        d="M32 32C32 14.33 46.33 0 64 0H320C337.7 0 352 14.33 352 32C352 49.67 337.7 64 320 64H290.5L301.9 212.2C338.6 232.1 367.5 265.4 381.4 306.9L382.4 309.9C385.6 319.6 383.1 330.4 377.1 338.7C371.9 347.1 362.3 352 352 352H32C21.71 352 12.05 347.1 6.04 338.7C.0259 330.4-1.611 319.6 1.642 309.9L2.644 306.9C16.47 265.4 45.42 232.1 82.14 212.2L93.54 64H64C46.33 64 32 49.67 32 32zM224 384V480C224 497.7 209.7 512 192 512C174.3 512 160 497.7 160 480V384H224z" />
                </svg>
            </div>
        </div>
        <div v-cloak class="p-2 pr-3 overflow-hidden">
            <div class="mb-8">
                <b>Details</b>
            </div>
            <ul v-for="(tableData, tableName) in store.panelRightData" class="space-y-3">
                <li v-for="(data, key) in tableData" class="flex justify-between">
                    <div v-text="store.panelRightKeys[tableName][key] + ': '" class="font-semibold text-ellipsis" :title="store.panelRightKeys[tableName][key]"></div>
                    <div v-text="data"></div>
                </li>
            </ul>
        </div>
    </div>
</div>
