<script setup>
import { ref, onMounted } from 'vue'
import { store } from './../store/store'

const dTable = ref(null)

onMounted(() => {
  $(document).ready(function () {
    dTable.value = $('#rpd-table').DataTable()
    // reinit table
    dTable.value.destroy()
  })
})
</script>
