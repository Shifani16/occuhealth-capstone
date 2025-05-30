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

/**
 * @param {string} type
 * @param {string} filename 
 * @param {number} targetWidth 
 * @param {number} targetHeight 
 * @param {number} [jpegQuality=0.9] 
 * @returns {string|null} 
 */
const exportAsImage = (type = 'image/png', filename = 'grafik.png', targetWidth = 600, targetHeight = 400, jpegQuality = 0.9) => {
    const chartInstance = barChartRef.value?.chart;

    if (chartInstance) {
        const offscreenCanvas = document.createElement('canvas');
        offscreenCanvas.width = targetWidth;
        offscreenCanvas.height = targetHeight;

        const offscreenCtx = offscreenCanvas.getContext('2d');

        const originalWidth = chartInstance.canvas.width;
        const originalHeight = chartInstance.canvas.height;

        chartInstance.resize(targetWidth, targetHeight);
        chartInstance.render(); 

        offscreenCtx.drawImage(chartInstance.canvas, 0, 0, targetWidth, targetHeight);

        chartInstance.resize(originalWidth, originalHeight);
        chartInstance.render(); 

        let dataUrl;
        if (type === 'image/jpeg') {
            dataUrl = offscreenCanvas.toDataURL(type, jpegQuality);
            console.log(`Generated JPEG data URL with quality ${jpegQuality}: ${dataUrl.substring(0, 100)}...`);
        } else {
            dataUrl = offscreenCanvas.toDataURL(type);
            console.log(`Generated PNG data URL: ${dataUrl.substring(0, 100)}...`);
        }

        const link = document.createElement('a');
        link.href = dataUrl;
        link.download = filename;

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