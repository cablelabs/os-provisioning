<script setup>
import { ref, onMounted } from 'vue'
import { store } from './../store/store'

const dTable = ref(null)

let propData = document.querySelector('#coremon-index-table').dataset
store.panelRightKeys = JSON.parse(propData.headers)

onMounted(() => {
  $(document).ready(function () {
    dTable.value = $('#coremon-datatable').DataTable()
    // reinit table
    dTable.value.destroy()

    let panelKeys = Object.keys(store.panelRightKeys)

		dTable.value.on('click', 'tr', function() {
			for (let cell of this.cells) {
        let panelKey = panelKeys[cell.cellIndex]
        store.panelRightData[panelKey] = cell.innerText

        store.minifiedRight = false
			}
		})
  })
})
</script>
