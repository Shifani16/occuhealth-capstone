<template>
    <Bar ref="barChartRef" :data="dataChart" :options="options" />
</template>

<script setup>
import { ref, defineExpose } from 'vue'; 
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

const props = defineProps({
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
          return ctx.raw !== undefined && ctx.raw !== null ? ctx.raw.toLocaleString() : '';
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

const barChartRef = ref(null);

const exportAsImage = (type = 'image/png', filename = 'grafik.png') => {
    const chartInstance = barChartRef.value?.chart;

    if (chartInstance) {
        const canvas = chartInstance.canvas;

        const dataUrl = canvas.toDataURL(type); 

        const link = document.createElement('a');
        link.href = dataUrl;
        link.download = filename; 

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } else {
        console.error("Chart instance not available for export.");
    }
};

defineExpose({
  exportAsImage,
  barRef: barChartRef 
});

</script>

<style scoped>
canvas {
    width: 100% !important; 
    height: 100% !important;
    max-width: 100% !important; 
    max-height: 100% !important; 
}
</style>