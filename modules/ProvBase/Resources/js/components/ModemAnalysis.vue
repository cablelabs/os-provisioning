<script>
import { ref } from 'vue'
import axios from 'axios'

export default {
  setup() {
    const propData = document.querySelector('#OpenSourceModemAnalysis').dataset

    // refs
    const loading = ref(true)
    const pingStarted = ref(false)
    const selectedPing = ref(1)
    const floodPingResult = ref(null)
    const pingOptions = [
      { id: 1, text: propData.modemAnalysisFloodpingLowLoad },
      { id: 2, text: propData.modemAnalysisFloodpingAverageLoad },
      { id: 3, text: propData.modemAnalysisFloodpingBigLoad },
      { id: 4, text: propData.modemAnalysisFloodpingHugeLoad },
    ]

    // methods
    function floodPing() {
      let timeout = {
        1: 5000,
        2: 10000,
        3: 30000,
        4: 30000,
      }

      snotify.success(propData.messagesAnalysisPingInProgress, null, {
        timeout: timeout[selectedPing.value],
      })
      pingStarted.value = true
      floodPingResult.value = ''

      axios({
        method: 'post',
        url: propData.routeModemFloodPing,
        headers: { 'X-CSRF-TOKEN': propData.csrfToken },
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        data: {
          task: selectedPing.value,
        },
      })
        .then((response) => {
          floodPingResult.value = response.data
          pingStarted.value = false
        })
        .catch((error) => {
          console.error(error)
          pingStarted.value = false
        })
    }

    return {
      loading,
      pingStarted,
      selectedPing,
      floodPingResult,
      pingOptions,
      floodPing,
    }
  },
}
</script>
