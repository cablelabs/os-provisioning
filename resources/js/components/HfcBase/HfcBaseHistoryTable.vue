<template>
<h3 class="m-b-20 ">History</h3>
<div style="overflow:hidden auto">
    <table class="table datatable m-b-0" style="width:100%;">
        <thead>
            <th></th>
            <th data-priority="1">Type</th>
            <th data-priority="5">Time</th>
        </thead>
        <tbody>
            <tr v-for="data in history" :key="data.statehistory_id">
                <td></td>
                <td>
                    <div class="d-flex align-items-baseline" :title="data.output" style="simple" data-toggle="tooltip" data-placement="top" data-boundary="window">
                        <i class="fa fa-circle" :class="data.last_hard_state >= 2 ? 'text-danger' : (data.last_hard_state == 1 ? 'text-warning' : 'text-success')"></i>
                        <span v-text="data.service"></span>
                    </div>
                </td>
                <td v-text="data.state_time"></td>
            </tr>
        </tbody>
    </table>
    <div class="d-flex justify-content-center m-t-20">
        <div id="loader" v-show="! init"></div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted, nextTick, computed } from 'vue'
import { store } from './../../store/store'

// prepare default vaules
let propData = document.querySelector('#HfcBase-history-table').dataset
let propData_url = propData.url
let propData_token = propData.token
let propData_viewjqueryall = propData.viewjqueryall
let language = {}

// refs
const init = ref(false)
const history = ref({})


// computed
const isWideScreen = computed(() => window.matchMedia('(min-width: 1700px)').matches)

onMounted(()=>{
    fetchLanguage()
    initial()
})

async function fetchLanguage(){
    const res = await axios.get('/admin/api/HfcBase/datatables-lang')
    if(res.data.success){
        language = res.data.result
    }
}
function initial(){
    axios({
        method: 'get',
        url: propData_url,
        headers: {'X-CSRF-TOKEN': propData_token},
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    })
    .then(async function (response) {
        store.hfcStorageRequest = response.data
        history.value = store.hfcStorageRequest.table
        init.value = true
        await nextTick()
        initDatatables()
    })
    .catch(function (error) {
        init.value = true
        // this.$snotify.error(error.message)
    })
}
function initDatatables() {
    $('table.datatable').DataTable({
        language,
        responsive: {
            details: {
                type: 'column', // auto resize the Table to fit the viewing device 
            }
        },
        autoWidth: false, // Option to ajust Table to Width of container
        dom: 'ltp', // sets order and what to show 
        order: [
            [ 2, "desc" ]
        ],
        lengthMenu:  [ [10, 25, 100, -1], [10, 25, 100, propData_viewjqueryall ] ], // Filter to List # Datasets
        pagingType: "simple",
        columnDefs: [
            { responsivePriority: 1, targets: 1 },
            { responsivePriority: 2, targets: -1 }
        ],
        aoColumnDefs: [ {
                className: 'control',
                orderable: false,
                searchable: false,
                targets:   [0]
            },
            {
                defaultContent: "",
                targets: "_all"
            }
        ],
    })
}
</script>
