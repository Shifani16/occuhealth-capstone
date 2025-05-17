<template>
    <Bar :data="dataChart" :options="options" />
</template>

<script setup>
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

defineProps({
  dataChart: {
    type: Object,
    required: true
  }
});

const options = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      callbacks: {
        label: function (ctx) {
          return ctx.raw.toLocaleString();
        }
      }
    }
  },
  scales: {
    y: {
      ticks: {
        beginAtZero: true
      }
    }
  }
};

</script>

<style scoped>
.chart-container {
  height: 300px;
}
</style>