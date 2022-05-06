<script setup>
import { onMounted } from 'vue'
import { store } from './../store/store'

onMounted(() => {
  if (typeof(Storage) === "undefined") {
    console.error("Sorry, no Web Storage Support - Cant save State of Sidebar - please update your Browser")
  }
})
</script>
