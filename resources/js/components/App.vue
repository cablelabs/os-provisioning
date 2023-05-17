<script>
import { onMounted, getCurrentInstance } from 'vue'
import { store } from '@/store/store'

export default {
  setup() {
    onMounted(() => {
      if (typeof Storage === 'undefined') {
        console.error('Sorry, no Web Storage Support - Cant save State of Sidebar - please update your Browser')
      }
      // prepare snotify
      window.snotify = getCurrentInstance().appContext.config.globalProperties.$snotify
    })

    return { store }
  }
}
</script>
