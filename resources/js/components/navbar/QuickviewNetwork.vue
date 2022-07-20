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
  active_alarms: { type: [String, Number], default: 0 },
  green_mode: { type: [String, Number], default: 0 },
  info: { type: [String, Number], default: 0 },
  warning: { type: [String, Number], default: 0 },
  critical: { type: [String, Number], default: 0 },
})

// data
const data = [parseInt(props.green_mode), parseInt(props.info), parseInt(props.warning), parseInt(props.critical)]

const QuickviewNetworkChart = ref('')
let options = reactive({
  type: 'doughnut',
  plugins: [ChartDataLabels],
  data: {
    datasets: [{
      data: data,
      backgroundColor: ["#7FB433", "#0EA5E9", "#EAB308", "#EF4444"],
    }]
  },
  options: {
    tooltips: {
      enabled: false
    },
    plugins: {
      datalabels: {
        align: 'right',
        offset: -15,
        display: true,
        backgroundColor: '#7FB433',
        borderRadius: 30,
        borderColor: '#fff',
        borderWidth: 2,
        padding: {right: 30, left: 30, top: 5},
        color: '#fff',
        font: {
          size: 16,
          weight: 'bold',
        },
        formatter: (value, ctx) => {
          return ctx.dataIndex === 0 ? `${parseInt((value/data.reduce((sum, el) => sum + el, 0))*100)}%` : ''
        },
        display: (ctx) => {
          return ctx.dataIndex === 0;
        }
      },
    }
  }
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
