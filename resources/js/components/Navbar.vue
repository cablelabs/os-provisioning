<script>
import { ref } from 'vue'

export default {
  setup() {
    function toggleMobileSidebar() {
      window.dispatchEvent( new Event('sidebar.toggle') );
    }

    const breadcrumbScroller = ref(false)

    const selectedRoute = ref('')
    const searchfield = ref()
    const showSearchbar = ref(false)
    const search = ref('')
    window.addEventListener('keypress', function (e) {
      if (
        ['input', 'textarea'].includes(document.activeElement.tagName.toLowerCase()) ||
        document.activeElement.contentEditable === 'true' ||
        e.ctrlKey ||
        e.metaKey ||
        e.altKey
      ) {
        return
      }

      showSearchbar.value = true
      search.value = e.key
      this.setTimeout(() => searchfield.value.focus(),25)
    })

    function blurInput(e) {
      e.target.blur()
      showSearchbar.value = false
    }

    return {
      breadcrumbScroller,
      searchfield,
      showSearchbar,
      toggleMobileSidebar,
      blurInput,
      search,
      selectedRoute,
    }
  }
}
</script>
