<script setup>
import { ref, onMounted } from 'vue'
import { store } from './../store/store'

const dTable = ref(null)

let propData = document.querySelector('#rpds').dataset
store.panelRightKeys = JSON.parse(propData.translatedFields)

onMounted(() => {
  $(document).ready(function () {
    dTable.value = $('#rpd-table').DataTable()
    // reinit table
    dTable.value.destroy()

		dTable.value.on('click', 'tr', function() {
			for (let cell of this.cells) {
        store.panelRightData[cell.cellIndex] = cell.innerText

        store.minifiedRight = false
			}
		})
  })
})
</script>
