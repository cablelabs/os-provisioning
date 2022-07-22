<template>
<canvas ref="QuickviewNetworkChart" class="w-full max-w-screen-sm"></canvas>
<div id="QuickviewNetworkDetail" class="text-center mt-3 pb-3 mx-auto">
  <div class="text-base font-bold">{{ title }}</div>
  <div class="font-medium font-bold">Active Alarms: {{ active_alarms }}</div>
  <ul class="text-gray-500 mt-3 text-left">
    <li class="info flex items-center max-h-6">{{ info }} Info</li>
    <li class="warning flex items-center max-h-6">{{ warning }} Warning</li>
    <li class="critical flex items-center max-h-6">{{ critical }} Critical</li>
  </ul>
</div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import {Chart} from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels';

// props
const props = defineProps({
  title: { type: String, required: true },
  active_alarms: { type: Number, default: 0 },
  info: { type: Number, default: 0 },
  warning: { type: Number, default: 0 },
  critical: { type: Number, default: 0 },
})

// data
const data = props.active_alarms == 0 ? [1] : [parseInt(props.info), parseInt(props.warning), parseInt(props.critical)]

const QuickviewNetworkChart = ref('')
let options = reactive({
  type: 'doughnut',
  plugins: [ChartDataLabels],
  data: {
    datasets: [{
      data: data,
      backgroundColor: props.active_alarms == 0 ? ['#7FB433'] : ['#0EA5E9', '#EAB308', '#EF4444'],
    }]
  },
  options: {
    tooltips: {
      enabled: false
    },
    plugins: {
      datalabels: {
        align: 'center',
        display: true,
        color: '#fff',
        font: {
          size: 10,
          weight: 'bold',
        },
        formatter: (value, ctx) => {
          return props.active_alarms == 0 ? 'No Alarm' : value == 0 ? '' : `${Math.round(((value/data.reduce((sum, el) => sum + el, 0))*100) * 10) / 10}%`
        },
      },
    },
  },
})

// mounted
onMounted(() => {
  new Chart(QuickviewNetworkChart.value, options);
})
</script>
 
<style scope>
 #QuickviewNetworkDetail{
   max-width: 140px
 }
 #QuickviewNetworkDetail ul li::before {
  content: "\2022";
  color: red;
  font-weight: bold;
  font-size: 32px;
  display: inline-block; 
  width: 15px;
  margin-left: 0.4em;
}
#QuickviewNetworkDetail ul li.info::before {
  color: #0EA5E9;
}
#QuickviewNetworkDetail ul li.warning::before {
  color: #EAB308;
}
#QuickviewNetworkDetail ul li.critical::before {
  color: #EF4444;
}
</style>
