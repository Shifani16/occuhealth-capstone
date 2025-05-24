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

const exportAsImage = (type = 'image/png', filename = 'grafik.png', targetWidth = 600, targetHeight = 400) => {
    const chartInstance = barChartRef.value?.chart;

    if (chartInstance) {
        const canvas = chartInstance.canvas;
        const currentWidth = canvas.width;
        const currentHeight = canvas.height;

        const offscreenCanvas = document.createElement('canvas');
        offscreenCanvas.width = targetWidth;
        offscreenCanvas.height = targetHeight;
        const ctx = offscreenCanvas.getContext('2d');

        const dataUrl = canvas.toDataURL(type); 

        const link = document.createElement('a');
        link.href = dataUrl;
        link.download = filename; 

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        console.log(`Generated data URL for image starting: ${dataUrl.substring(0, 100)}...`); // Confirm non-empty data URL
        return dataUrl;
    } else {
        console.error("Chart instance not available for export.");
        return null;
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