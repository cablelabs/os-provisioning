<script setup>
import { ref, reactive, onMounted } from 'vue'

const propData = document.querySelector('#modem-analysis').dataset

// refs
const loading = ref(true)
const pingStarted = ref(false)
const selectedPing = ref(1)
const floodPingResult = ref(null)
const pingOptions = reactive([
  {id: 1, name: propData.modemAnalysisFloodpingLowLoad},
  {id: 2, name: propData.modemAnalysisFloodpingAverageLoad},
  {id: 3, name: propData.modemAnalysisFloodpingBigLoad},
  {id: 4, name: propData.modemAnalysisFloodpingHugeLoad},
])

// methods
function floodPing() {
  let timeout = {
    1: 5000,
    2: 10000,
    3: 30000,
    4: 30000
  }

  this.$snotify.success(propData.messagesAnalysisPingInProgress, null, {timeout: timeout[selectedPing.value]})
  pingStarted.value = true
  floodPingResult.value = ''

  axios({
    method: 'post',
    url: '{{ route("Modem.floodPing", ["modem" => $modem->id]) }}',
    headers: {'X-CSRF-TOKEN': propData.csrfToken},
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    data: {
      task: selectedPing.value
    }
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
</script>
