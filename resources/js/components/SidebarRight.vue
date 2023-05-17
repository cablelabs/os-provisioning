<script>
import { onMounted, ref } from 'vue'
import { store } from '@/store/store'

export default {
  setup() {
    const pinned = ref(false)

    onMounted(() => {
      if (document.getElementById('sidebar-right')) {
        initSidebar()
      }
    })

    function initSidebar() {
      pinned.value = localStorage.getItem('pinned-right') === 'true'

      if (pinned.value) {
        store.minifiedRight = !pinned.value
      }
    }

    function initSidebar() {
      pinned.value = !pinned.value
      localStorage.setItem('pinned-right', pinned.value)
    }

    function minifySidebarRight() {
      store.minifiedRight = !store.minifiedRight

      if (pinned.value) {
        pinSidebarRight()
      }
    }

    return {
      pinned,
      initSidebar,
      initSidebar,
      minifySidebarRight,
      store
    }
  }
}
</script>
