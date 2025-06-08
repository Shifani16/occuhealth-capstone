      
<template>
    <Bar ref="barChartRef" :data="dataChart" :options="options" />
</template>

<script setup>
import { ref, defineExpose, nextTick } from 'vue'; 
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
        },
    },
    scales: {
        y: {
            ticks: {
                beginAtZero: true
            }
        },
        x: {
             ticks: {
                 autoSkip: false, 
                 maxRotation: 45,
                 minRotation: 0 
             }
        }
    },
};

const barChartRef = ref(null);

/**
 * @param {string} type
 * @param {string} filename 
 * @param {number} [jpegQuality=0.9] 
 * @param {boolean} [triggerDownload=true] 
 * @returns {Promise<string|null>}
 */
const exportAsImage = (type = 'image/png', filename = 'grafik.png', jpegQuality = 0.9, triggerDownload = true) => {
    const chartInstance = barChartRef.value?.chart;

    if (!chartInstance || !chartInstance.canvas) {
        console.error("[Export Error] Chart instance or canvas not available for export.");
        return Promise.resolve(null); 
    }

    const canvas = chartInstance.canvas;

    return new Promise((resolve, reject) => {
        const renderDelay = 100;

        nextTick(() => {
            setTimeout(() => {
                try {
                    console.log(`[Export Debug] Canvas size BEFORE toDataURL: ${canvas.width}x${canvas.height}`);
                    console.log("[Export Debug] Chart Data snapshot:", props.dataChart); 

                    if (canvas.width <= 0 || canvas.height <= 0) {
                        console.error(`[Export Error] Canvas has zero or negative dimensions (${canvas.width}x${canvas.height}). Cannot export.`);
                        resolve(null);
                        return;
                    }

                    let dataUrl = null;

                    if (type === 'image/jpeg') {
                         const tempCanvas = document.createElement('canvas');
                         const tempCtx = tempCanvas.getContext('2d');
                         tempCanvas.width = canvas.width;
                         tempCanvas.height = canvas.height;

                         tempCtx.fillStyle = '#FFFFFF';
                         tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

                         tempCtx.drawImage(canvas, 0, 0);

                         dataUrl = tempCanvas.toDataURL(type, jpegQuality);
                         console.log(`[Export Debug] Generated JPEG data URL (start): ${dataUrl.substring(0, 50)}...`);

                    } else {
                        dataUrl = canvas.toDataURL(type);
                        console.log(`[Export Debug] Generated PNG data URL (start): ${dataUrl.substring(0, 50)}...`);
                    }

                    if (dataUrl && triggerDownload) {
                        const link = document.createElement('a');
                        link.href = dataUrl;
                        link.download = filename;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        console.log(`[Export Debug] Download triggered for ${filename}`);
                    }

                    resolve(dataUrl); 

                } catch (error) {
                    console.error("[Export Error] Error during chart image generation:", error);
                    reject(error);
                }
            }, renderDelay);
        });
    });
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
    display: block; 
}

.chart-container {
    height: 400px; 
    width: 100%; 
    position: relative; 
}
</style>

    